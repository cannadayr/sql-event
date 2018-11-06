with recursive time_range(time_ref) as (
    select time_ref from start_moment    
    union all
    select datetime(time_ref,(select rate from tick)) from time_range
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
    select '2018-01-01 02:00:00' as time_ref
)
select * from time_range;
