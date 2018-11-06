with recursive time_range(this_time_ref) as (
    select
        (select time_ref from start_moment)
    union all

    select

        datetime(this_time_ref,(select rate from tick))

    from time_range

    limit (
        cast(strftime((select unit from tick_unit),(select time_ref from end_moment)) as integer)
        - cast(strftime((select unit from tick_unit),(select time_ref from start_moment)) as integer)
    )
),
tick(rate) as (
    select '+1 seconds' as rate
),
tick_unit(unit) as (
    select '%s' as unit
),
start_moment(time_ref) as (
    select '2018-01-01 01:30:00' as time_ref
),
end_moment(time_ref) as (
    --select '2018-01-01 01:30:04' as time_ref
    select '2018-01-01 03:30:10' as time_ref
),
this_entity(id) as (
    select 1
)

select
    time_range.this_time_ref,
    (
        select
            id

        from event

        where
            entity_id = 1
            and end_time < time_range.this_time_ref

        order by end_time desc

        limit 1
    ) as prev_event_id

from
    time_range
;

