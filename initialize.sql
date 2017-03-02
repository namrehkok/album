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


update media set isfront = false

update media set isfront = false where id in
(
	select id from
	(
	select b.id, row_number() over (partition by a.id order by 1) rn from 
	albums a left join media b on a.id = b.album_id and b.isfront = true and media = 'jpg'
	) t
	where rn > 1
)
;

update media set isfront = true where id in 
( select id from
(
select b.id, row_number() over (partition by a.id order by 1) as rn from
(select a.id from 
albums a left join media b on a.id = b.album_id and b.isfront = true and media = 'jpg') a
join media b on a.id = b.album_id
) t
where rn = 1
)
;

SELECT a.name, a.slug, min(cast(b.createddate as date)) as startdate, max(cast(b.createddate as date)) as enddate,
max(case when b.isfront = true then b.thumb_small else null end) as thumb_small
FROM albums a join media b on a.id = b.album_id group by 1 ,2 order by 3 desc

update media set isfront = false where id in
(select id from media a join
(select album_id from media where id = 600) b on a.album_id = b.album_id);

update media set isfront = true where id = 600;