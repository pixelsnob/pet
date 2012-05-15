/* Digital products */

select p.*, d.name as dlname
from pet.products p
left join pet.product_types pt
on p.product_type_id = pt.id
left join pet.products_downloads pd
on p.id = pd.product_id
left join pet.downloads d
on pd.download_id = d.id
where pt.id = 1;

select category, code, name, price, sd.title as dlname
from pet_old.sales_product sp
left join pet_old.sales_download sd
on sp.download_id = sd.id
where sp.category = 'digital';

/* Physical products */

select p.*, pp.name as prod_name, s.*
from pet.products p
left join pet.product_types pt
on p.product_type_id = pt.id
left join pet.physical_products pp
on p.id = pp.product_id
left join pet.shipping s
on pp.shipping_id = s.id
where pt.id = 2;

select category, code, name, price, ss.*
from pet_old.sales_product sp
left join pet_old.sales_shipping ss
on sp.shipping_id = ss.id
where sp.category = 'physical';

/* Course products */

select p.*, c.name as course_name
from pet.products p
left join pet.product_types pt
on p.product_type_id = pt.id
left join pet.products_courses pc
on p.id = pc.product_id
left join pet.courses c
on pc.course_id = c.id
where pt.id = 3;

select category, code, sp.name, price, sc.name
from pet_old.sales_product sp
left join pet_old.streams_course sc
on sp.course_id = sc.id
where sp.category = 'stream';

/* Subscription products */

select p.*, s.name as subscription_name
from pet.products p
left join pet.product_types pt
on p.product_type_id = pt.id
left join pet.products_subscriptions ps
on p.id = ps.product_id
left join pet.subscriptions s
on ps.subscription_id = s.id
where pt.id = 4;

select category, code, name, price, zone, term, is_renewal
from pet_old.sales_product
where category = 'subscription';

/* Promos */

select id, code, expiration, discount, extra_days, uses
from promos order by code;

select id, code, expiration, discount, extra_days, uses
from sales_promo order by code;

/* Find duplicate emails in pet.users */

/*
select au1.*, so1.id so1_id, au2.*, so2.id so2_id
from auth_user au1
left join auth_user au2
on (au1.email = au2.email and au1.id != au2.id)
left join sales_order so1
on au1.id = so1.user_id
left join sales_order so2
on au2.id = so2.user_id
where au1.email != ''
group by au2.id
having au2.id is not null;
*/

select x.*, u.email, u.id, o.id oid
from users u, 
(
select count(*) ct, id, email
from users
where email is not null
group by email
having ct > 1
) x, orders o
where u.email = x.email
and u.id != x.id
and o.user_id = u.id;

/* Pull an order */

select o.*, group_concat(p.sku) as skus, group_concat(op2.amount) as payments, 
pr.code as promo, pr.description as promo_desc
from orders o
left join ordered_products op
on o.id = op.order_id
left join view_products p
on op.product_id = p.id
left join promos pr
on o.promo_id = pr.id
left join order_payments op2
on o.id = op2.order_id
where o.id = 1128226
group by o.id\G

/* Pull a customer and profile */

select u.*, up.*, group_concat(o.id) as orders
from users u
left join user_profiles up
on u.id = up.user_id
left join orders o
on u.id = o.user_id
where u.id = 1126647\G

/*****************/

/* view_products */

select p.*, d.name, d.description
from products p
left join products_downloads pd
on p.id = pd.product_id
left join downloads d
on pd.download_id = d.id
where p.product_type_id = 1

union

select p.*, pp.name, pp.description
from products p
left join physical_products pp
on p.id = pp.product_id
where p.product_type_id = 2

union

select p.*, c.name, c.description
from products p
left join products_courses pc
on p.id = pc.product_id
left join courses c
on pc.course_id = c.id
where p.product_type_id = 3

union

select *
from products p
left join products_subscriptions ps
on p.id = ps.product_id
left join subscriptions s
on ps.subscription_id = s.id
where p.product_type_id = 4;