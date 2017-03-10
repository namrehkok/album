select * from
(
	select 
	rank() over (partition by album_id, x, xx order by original desc) rnk
	, lag(createddate) over (partition by album_id order by original desc) prev_created
	, t.* 
	from
	(
		select 
		case when createddate is null then 0 else 1 end x,
		lag(case when createddate is null then 0 else 1 end) over (partition by album_id order by original desc) xx
		, t.* 
		from media t where album_id = 110
		order by original desc
	) t
) u
--where x+xx = 1
order by original desc



select
id, max(expire) over (order by id rows between unbounded preceding and current row) as expire
from t
order by id desc

update media set
createddate = ad
select * from
(
select 
t.*,
max(createddate) over (partition by album_id order by original rows between unbounded preceding and current row) ad
from 
media t
) t
where (createddate < current_date - 21000 or createddate is null)
and id = 97


update media 
set createddate = (
select ad from
(
select 
t.*,
max(createddate) over (partition by album_id order by original rows between unbounded preceding and current row) ad
from 
media t
) t
where (createddate < current_date - 21000 or createddate is null)
and t.id = media.id and t.id = 306
and exists (select 1 from media x where (createddate < current_date - 26000 or createddate is null) and media.id = x.id)
)


create table media_backup_t as
select * from media_backup

select * from media a join media_backup b on a.id = b.id where a.createddate <> coalesce(b.createddate, current_date)
select * from media_backup where id = 97
select * from media where id = 97

select * from
(
select 
t.*,
max(createddate) over (partition by album_id order by original rows between unbounded preceding and current row) ad
from 
media t
) t
where (createddate < current_date - 21000 or createddate is null)
and t.id = 306

select * from media where createddate is not null and id = 97

drop table test;
create table test
(id integer,
a integer);

insert into test values (1,1);
insert into test values (1,2);
insert into test values (1,null);

select * from test

update test set a = (select max(a) from test t where test.id = t.id group by id)


update media as v
set createddate = s.ad
from
(
	select * from
	(
	select 
	t.*,
	max(createddate) over (partition by album_id order by original rows between unbounded preceding and current row) ad
	from 
	media t
	) t
	where t.createddate is null
)
as s
where s.id = v.id



select * from media limit 50 offset 90000

select * from albums  where name like '%namreh%'