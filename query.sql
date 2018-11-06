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

    order by start_time desc

    limit 1
)
select * from prev_event
union all
select * from next_event;
