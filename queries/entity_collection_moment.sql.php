<?php
# vim: syn=sql
?>
with entity_collection(id) as (
    select
        id

    from entity

    where 1 = 1
        <?= $guest_clause ?>
        <?= $storage_clause ?>

    group by id
),
moment(this_time_ref) as (
    select '<?= $time ?>' as this_time_ref
),
new_event(duration) as (
    select '+<?= $duration ?> seconds'
)

select
    moment.this_time_ref,
    entity_collection.id as entity_id,
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
    prev_event.num_guests as prev_event_num_guests,
    prev_event.num_storage as prev_event_num_storage,
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
    (select case when
        (
            datetime(moment.this_time_ref,"-" || cast((3600 + (coalesce(prev_event.num_guests,1) * 30 * 60)) as text) || " seconds") >= coalesce(prev_event.end_time,'1970-01-01 00:00:00')
            and datetime(datetime(moment.this_time_ref,"+600 seconds"),(select duration from new_event)) <= coalesce(next_event.start_time,'9999-12-31 00:00:00')
            and (select not count(id) from event where start_time <= moment.this_time_ref and end_time >= moment.this_time_ref)
        )
        then "available" else "unavailable"
    end) as availability


from
    moment,
    entity_collection

left join event prev_event on prev_event.id = prev_event_id

left join event next_event on next_event.id = next_event_id
;

