with entity_collection(id) as (
    select
        id

    from entity

    group by id
),
moment(this_time_ref) as (
    select '2018-01-01 01:05:00' as this_time_ref
),
new_event(duration) as (
    select '+1800 seconds' -- 30 minutes
)

select
    moment.this_time_ref,
    entity_collection.id,
    (
        select
            id

        from event

        where
            entity_id = entity_collection.id
            and end_time < moment.this_time_ref

        order by end_time desc

        limit 1
    ) as prev_event_id,
    prev_event.start_time as prev_event_start_time,
    prev_event.end_time as prev_event_end_time,
    (
        select
            id

        from event

        where
            entity_id = entity_collection.id
            and start_time > moment.this_time_ref

        order by start_time asc

        limit 1
    ) as next_event_id,
    next_event.start_time as next_event_start_time,
    next_event.end_time as next_event_end_time,
    case when
        ( -- #TODO calculate cleanup and setup times from previous/current event
            datetime(moment.this_time_ref,"-600 seconds") >= coalesce(prev_event.end_time,'1970-01-01 00:00:00')
            and datetime(datetime(moment.this_time_ref,"+600 seconds"),(select duration from new_event)) <= coalesce(next_event.start_time,'9999-12-31 00:00:00')
            and (select not count(id) from event where start_time <= moment.this_time_ref and end_time >= moment.this_time_ref)
        )
        then "available" else "unavailable"
    end availability

from
    moment,
    entity_collection

left join event prev_event on prev_event.id = prev_event_id

left join event next_event on next_event.id = next_event_id
;

