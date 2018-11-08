/*<?php
# vim: syn=sql
?>*/
with recursive time_range(this_time_ref) as (
    select
        (select time_ref from start_moment)
    union all

    select

        datetime(this_time_ref,(select rate from tick))

    from time_range

    limit (
        (cast(strftime((select unit from tick_unit),(select time_ref from end_moment)) as integer)
        - cast(strftime((select unit from tick_unit),(select time_ref from start_moment)) as integer)) / (select ratio from tick_ratio)
    )
),
tick(rate) as (
    select '+60 seconds' as rate
),
tick_unit(unit) as (
    select '%s' as unit -- seconds
),
tick_ratio(ratio) as (
    select rtrim(ltrim((select rate from tick),'+'),' seconds')
),
start_moment(time_ref) as (
    select '2017-12-31 23:00:00' as time_ref
),
end_moment(time_ref) as (
    select '2018-01-01 07:00:00' as time_ref
),
this_entity(id) as (
    select 1
),
new_event(duration) as (
    select '+1800 seconds' -- 30 minutes
)

select
    time_range.this_time_ref,
    (
        select
            id

        from event

        where
            entity_id = (select id from this_entity)
            and end_time < time_range.this_time_ref

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
            entity_id = (select id from this_entity)
            and start_time > time_range.this_time_ref

        order by start_time asc

        limit 1
    ) as next_event_id,
    next_event.start_time as next_event_start_time,
    next_event.end_time as next_event_end_time,
    case when
        ( -- #TODO calculate cleanup and setup times from previous/current event
            datetime(time_range.this_time_ref,"-600 seconds") >= coalesce(prev_event.end_time,'1970-01-01 00:00:00')
            and datetime(datetime(time_range.this_time_ref,"+600 seconds"),(select duration from new_event)) <= coalesce(next_event.start_time,'9999-12-31 00:00:00')
            and (select not count(id) from event where start_time <= time_range.this_time_ref and end_time >= time_range.this_time_ref)
        )
        then "available" else "unavailable"
    end availability

from
    time_range

left join event prev_event on prev_event.id = prev_event_id

left join event next_event on next_event.id = next_event_id
;

