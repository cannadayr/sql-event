with moment(time_ref) as (
    select '2018-01-01 01:30:00' as time_ref
),
this_entity(id) as (
    select 1
),
prev_event(id,start_time,end_time) as (
    select
        id,
        start_time,
        end_time

    from event

    where
        entity_id = (select id from this_entity)
        and end_time < (select time_ref from moment)

    order by end_time asc

    limit 1
),
next_event(id,start_time,end_time) as (
    select
        id,
        start_time,
        end_time

    from event

    where
        entity_id = (select id from this_entity)
        and start_time > (select time_ref from moment)

    order by start_time asc

    limit 1
),
prev_event_threshold(threshold) as (
    select "-1800 seconds" -- 30 mins
),
next_event_threshold(threshold) as (
    select "+1800 seconds" -- 30 mins
)
select
    this_moment.id,
    this_moment.time_ref,
    this_moment.prev_event_threshold,
    this_moment.next_event_threshold,
    this_moment.prev_event_end_time,
    this_moment.next_event_start_time,
    case when
        this_moment.prev_event_threshold >= this_moment.prev_event_end_time
        and this_moment.next_event_threshold <= this_moment.next_event_start_time
        then "available" else "unavailable"
    end availability
from (
    select
        (select id from this_entity) as id,
        (select time_ref from moment) as time_ref,
        datetime(
            (select time_ref from moment),
            (select threshold from prev_event_threshold)
        ) as prev_event_threshold,
        datetime(
            (select time_ref from moment),
            (select threshold from next_event_threshold)
        ) as next_event_threshold,
        (select end_time from prev_event) as prev_event_end_time,
        (select start_time from next_event) as next_event_start_time
    ) this_moment
;
