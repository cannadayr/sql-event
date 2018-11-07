insert into entity (max_guests,max_storage) values(2,1);
insert into entity (max_guests,max_storage) values(2,0);
insert into entity (max_guests,max_storage) values(1,2);
insert into entity (max_guests,max_storage) values(1,0);

-- insert into event (start_time,end_time,entity_id) values ('2018-01-01 00:00:00','2018-01-01 00:00:00',1);
insert into event (start_time,end_time,entity_id,num_guests,num_storage) values ('2018-11-01 15:00:00','2018-11-02 10:00:00',1,1,1);
insert into event (start_time,end_time,entity_id,num_guests,num_storage) values ('2018-11-04 15:00:00','2018-11-05 10:00:00',1,1,1);
---
insert into event (start_time,end_time,entity_id,num_guests,num_storage) values ('2018-11-03 15:00:00','2018-11-04 10:00:00',2,1,0);
insert into event (start_time,end_time,entity_id,num_guests,num_storage) values ('2018-11-04 15:00:00','2018-11-05 10:00:00',2,1,0);
---
insert into event (start_time,end_time,entity_id,num_guests,num_storage) values ('2018-11-01 15:00:00','2018-11-02 10:00:00',3,1,1);
---
insert into event (start_time,end_time,entity_id,num_guests,num_storage) values ('2018-11-04 15:00:00','2018-11-05 10:00:00',4,1,0);
