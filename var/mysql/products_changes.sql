alter table products add name varchar(100) after sku;
alter table products add description text after name;
alter table products add short_description varchar(150) after description;

/* Courses */

update products p, courses c
set p.name = c.name,
p.description = c.description,
p.short_description = c.name
where p.id = c.product_id;

alter table courses drop column description;
alter table courses drop column name;

/* Digital subs */
update products p, digital_subscriptions d
set p.name = d.name,
p.description = d.description,
p.short_description = d.name
where p.id = d.product_id;

alter table digital_subscriptions drop column description;
alter table digital_subscriptions drop column name;

/* Physical prods */
update products p, physical_products pp
set p.name = pp.name,
p.description = pp.description,
p.short_description = pp.name
where p.id = pp.product_id;

alter table physical_products drop column description;
alter table physical_products drop column name;

/* Subscriptions */
update products p, subscriptions s
set p.name = s.name,
p.description = s.description,
p.short_description = s.name
where p.id = s.product_id;

alter table subscriptions drop column description;
alter table subscriptions drop column name;

/* Downloads */
update products p, downloads d
set p.name = d.name,
p.description = d.description,
p.short_description = d.name
where p.id = d.product_id;

alter table downloads drop column description;
alter table downloads drop column name;

drop view view_products;

create view `view_products` AS
select p.*,
pt.name as product_type
from products p
left join product_types pt
on p.product_type_id = pt.id
left join downloads d
on p.id = d.product_id
left join digital_subscriptions ds
on p.id = ds.product_id
left join subscriptions s
on p.id = s.product_id
left join physical_products pp
on p.id = pp.product_id
left join courses c
on p.id = c.product_id;