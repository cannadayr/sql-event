create table entity (
    id integer primary key,
    max_guests integer,
    max_storage integer
);

create table event (
    id integer primary key,
    start_time timestamp default null,
    end_time timestamp default null,
    entity_id integer,
    num_guests integer,
    num_storage integer
);
