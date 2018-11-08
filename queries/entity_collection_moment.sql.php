/*<?php
# vim: syn=sql
?>*/
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
new_event(duration,num_guests) as (
    select
        '+<?= $duration ?> seconds' as duration,
        '<?= $guests?>' as num_guests
)

select
    *,
    case when
        (prev_event_threshold >= coalesce(prev_event_end_time,'1970-01-01 00:00:00') -- 1
         and next_event_threshold <= coalesce(next_event_start_time,'9999-12-31 00:00:00')
         and not is_current_event
        ) then "available" else "unavailable"
    end availability

from (
    select
        *,
        (select coalesce(datetime(prev_event_end_time,prev_event_cleanup),'9999-12-31 23:00:00')) as prev_event_threshold,
        (select coalesce(datetime(datetime(this_time_ref,this_event_cleanup),new_event_duration),'1970-01-01 00:00:00')) as next_event_threshold

    from (
        select
            moment.this_time_ref as this_time_ref,
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
            new_event.duration as new_event_duration,
            new_event.num_guests as new_event_num_guests,
            (select "-" || cast((3600 + (coalesce(prev_event.num_guests,0) * 30 * 60)) as text) || " seconds") as prev_event_cleanup,
            (select "+" || cast((3600 + (coalesce(new_event.num_guests,0) * 30 * 60)) as text) || " seconds") as this_event_cleanup,
            (select count(id) from event where start_time <= moment.this_time_ref and end_time >= moment.this_time_ref and entity_id = entity_collection.id) as is_current_event

        from
            moment,
            entity_collection,
            new_event

        left join event prev_event on prev_event.id = prev_event_id

        left join event next_event on next_event.id = next_event_id
    )
)
;

