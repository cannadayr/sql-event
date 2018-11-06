create table entity (
    id integer primary key
);

create table event (
    id integer primary key,
    start_time timestamp default null,
    end_time timestamp default null,
    entity_id integer
);
