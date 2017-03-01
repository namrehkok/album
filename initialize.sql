drop user album;

create user album with password 'album';

grant connect on database album to album;
grant all privileges on database album to album;


drop table albums cascade;
drop table media cascade;

CREATE TABLE albums (
  id serial primary key ,
  subdir varchar(50) unique,
  name varchar(50),
  slug varchar(50)
);

create table media (
  id serial primary key,
  media varchar(3), --JPG / MP4
  album_id integer REFERENCES albums(id) ON DELETE CASCADE,
original varchar(100) unique,
thumb_small varchar(100) unique,
thumb_large varchar(100) unique,
createddate timestamp,
addeddate timestamp default current_timestamp,
favorite boolean default true,
isfront boolean default false
);

alter table albums add column front integer references media(id);

grant all on table albums to album;
grant all on table media to album;


GRANT USAGE, SELECT ON SEQUENCE albums_id_seq TO album;
GRANT USAGE, SELECT ON SEQUENCE media_id_seq TO album;

select * from albums;
select * from media;

select a.name, cast(min(createddate) as date), max(createddate) from 
albums a join media b on a.id = b.album_id
group by 1
;


commit

delete from albums cascade;
delete from media cascade;

insert into albums (name, slug) values ('test', 'slugtest');
insert into media (album_id, filename) values ((select min(id) as id from albums) , 'filename1');
insert into media (album_id, filename) values ((select min(id) as id from albums) , 'filename2');
insert into media (album_id, filename) values ((select min(id) as id from albums) , 'filename3');
insert into media (album_id, filename) values ((select min(id) as id from albums) , 'filename4');
insert into media (album_id, filename) values ((select min(id) as id from albums) , 'filename5');
insert into media (album_id, filename) values ((select min(id) as id from albums) , 'filename65');
insert into media (album_id, filename) values ((select min(id) as id from albums) , 'filename7');
insert into media (album_id, filename) values ((select min(id) as id from albums) , 'filename8');
insert into media (album_id, filename) values ((select min(id) as id from albums) , 'filename9');



insert into albums (name, subdir, slug) values ('fillipijnen', '/Fillipijnen', 'fillipijnen');

SELECT a.name, a.slug, min(b.createddate) as createddate, max(b.createddate) as createddate FROM albums a join media b on a.id = b.album_id group by 1 ,2 order by 3 desc