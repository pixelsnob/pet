/**
 * Initializes new PET DB, and migrates data from the old DB.
 * 
 * 
 */

/***************************************************************************************************
  Products
 **************************************************************************************************/
 
insert into product_types values (1, 'Download', 'download', 'Downloads'), (2, 'Physical', 'physical', 'Physical Products'),
(3, 'Course', 'course', 'Courses'), (4, 'Subscription', 'subscription', 'Subscriptions'),
(5, 'Digital Subscription', 'digital_subscription', 'Digital Subscriptions');

/* Digital products */

insert into download_formats
select * from pet_old.sales_download_format;

insert into products
(product_type_id, sku, cost, image, active) 
select 1, code, price, image, 1
from pet_old.sales_product
where category = 'digital';

insert into downloads
select null, p.id, sd.format_id, title, `desc`, date, path, size, thumb, subscriber_only
from products p 
left join pet_old.sales_product sp
on p.sku = sp.code
left join pet_old.sales_download sd
on sp.download_id = sd.id
where p.product_type_id = 1;

/* Physical products */

insert into shipping_zones
select * from pet_old.sales_shipping;

insert into products
(product_type_id, sku, cost, image, active) 
select 2, code, price, image, 0
from pet_old.sales_product
where category = 'physical';

update products set active = 1
where id in (169, 172, 170);

insert into physical_products
select null, p.id, sp.shipping_id, sp.name, sp.description, 0
from products p
left join pet_old.sales_product sp
on p.sku = sp.code
where p.product_type_id = 2;

/* Courses */

insert into products
(product_type_id, sku, cost, image, active) 
select 3, code, price, image, 1
from pet_old.sales_product
where category = 'stream';

insert into courses
select sc.id, p.id, sc.name, sp.description, sc.slug, sc.live, sc.free
from pet_old.streams_course sc
left join pet_old.sales_product sp
on sc.id = sp.course_id
left join products p
on p.sku = sp.code;

/* Subscriptions */

insert into products
(product_type_id, sku, cost, image, active, max_qty) 
select 4, code, price, image, 1, 1
from pet_old.sales_product
where category = 'subscription';

insert into subscription_zones values
(1, 'Canada', 'can'), (2, 'USA', 'usa'), (3, 'International', 'intl');

insert into subscriptions
select null, p.id,
if (sp.zone = 'usa', 2, if (sp.zone = 'can', 1, if (sp.zone = 'int', 3, null))),
sp.name, sp.description, (term * 12)/* Term in old db was in years, convert to months*/,
is_renewal
from products p
left join pet_old.sales_product sp
on p.sku = sp.code
where p.product_type_id = 4;

/* Gift subscriptions */

update products set is_giftable = 1
where id >= 181 and id <= 192;

/* Digital Subscriptions */

insert into products values
(300, 5, 'DIGITAL-MONTHLY', 4.25, '', 1, 1, 1),
(301, 5, 'DIGITAL-MONTHLY-RENEWAL', 4.25, '', 1, 1, 0),
(302, 5, 'DIGITAL-YEARLY', 39, '', 1, 1, 1),
(303, 5, 'DIGITAL-YEARLY-RENEWAL', 39, '', 1, 1, 0);

insert into digital_subscriptions values
(1, 300, 'Digital Subscription, Monthly', '', 0, 1, 1),
(2, 301, 'Digital Subscription, Monthly, Renewal', '', 1, 1, 1),
(3, 302, 'Digital Subscription, Yearly', '', 0, 0, 12),
(4, 303, 'Digital Subscription, Yearly, Renewal', '', 1, 0, 12);

/* Add products that were deleted that still exist in the ordered_products table */

insert into products (product_type_id, sku, cost, image, active) values
(2, 'BODVD1-2', 0, '', 0), (2, 'BODVD1-2-3', 0, '', 0), (2, 'BODVD2-3', 0, '', 0),
(4, 'E6BK', 0, '', 0), (4, 'E7BK', 0, '', 0), (4, 'V2N9', 0, '', 0), (4, 'V3N9', 0, '', 0);

insert into physical_products values
(null, 304, 1, 'BODVD1-2', null, 1),
(null, 305, 1, 'BODVD1-2-3', null, 1),
(null, 306, 1, 'BODVD2-3', null, 1);

insert into subscriptions values
(null, 307, 1, 'E6BK', null, 12, 0),
(null, 308, 1, 'E7BK', null, 12, 0),
(null, 309, 1, 'V2N9', null, 12, 0),
(null, 310, 1, 'V3N9', null, 12, 0);

/***************************************************************************************************
  Promotions
 **************************************************************************************************/
 
insert into promos
select id, code, expiration, description, public_description,
receipt_description, banner, discount, extra_days, uses
from pet_old.sales_promo;

insert into promo_products
select null, spp.promo_id, p.id
from pet_old.sales_promo_products spp
left join pet_old.sales_promo sp
on spp.promo_id = sp.id
left join products p
on spp.product_id = p.sku;


/***************************************************************************************************
  Users/profiles
 **************************************************************************************************/

/* Emails won't be unique until later... */

alter table users drop index email;

insert into users
select id, username, first_name, last_name, if (email = '', null, lower(email)), password,
is_staff, is_active, is_superuser, last_login, date_joined
from pet_old.auth_user;

show warnings;

/* User profiles */

/* There are user profiles that reference user_ids of users that no
   longer exist. Join to auth_user table. Discard if not in auth_user, per Tom. */

insert into user_profiles
select sp.id, sp.user_id, sp.address, sp.address_2, sp.company,
sp.city, sp.state, sp.postal_code,
sp.country, sp.phone,
if (isnull(sp.first_name_shipping), '', sp.first_name_shipping),
if (isnull(sp.last_name_shipping), '', sp.last_name_shipping),
if (isnull(sp.address_shipping), '', sp.address_shipping),
if (isnull(sp.address_2_shipping), '', sp.address_2_shipping),
if (isnull(sp.company_shipping), '', sp.company_shipping),
if (isnull(sp.city_shipping), '', sp.city_shipping),
if (isnull(sp.state_shipping), '', sp.state_shipping),
if (isnull(sp.postal_code_shipping), '', sp.postal_code_shipping),
if (isnull(sp.country_shipping), '', sp.country_shipping),
if (isnull(sp.phone_shipping), '', sp.phone_shipping),
sp.marketing, sp.occupation, sp.optIn, sp.optInPartner,
if (isnull(sp.optInSubscriber), 0, sp.optInSubscriber),
sp.comp, if (isnull(sp.version), '', sp.version),
if (isnull(sp.platform), '', sp.platform)
from pet_old.subscriber_profile sp
left join pet_old.auth_user u
on sp.user_id = u.id
where u.id is not null;

show warnings;

/* Current expirations */

insert into order_product_subscriptions
select null, u.id, null, sp.expiration, 0
from users u
left join pet_old.subscriber_profile sp
on u.id = sp.user_id
where sp.expiration is not null;

/* Previous expirations */

insert into order_product_subscriptions
select null, u.id, null, so.previous_expiration, 0
from users u
left join pet_old.sales_order so
on u.id = so.user_id
where so.previous_expiration is not null;

/* There are duplicate emails in the users table. Change any references in the user_subscriptions
   table, because we are about to delete the duplicate users. */
delete from order_product_subscriptions where user_id in (884338, 893654, 1078835, 809224, 866185,
887013, 902945, 1079210, 881059, 87670873, 843818);

/* These users don't exist anymore */

delete from pet_old.subscriber_profile_note where id in (23, 24);

insert into user_notes (select id, profile_id, rep_id, body, date from pet_old.subscriber_profile_note);


/***************************************************************************************************
  Orders
 **************************************************************************************************/
 
insert into orders
select so.id, so.user_id, sop.promo_id, so.date, so.date, so.email, so.first_name, so.last_name,
so.address, so.address_2, so.company, so.city, so.country, so.state, so.postal_code, so.phone,
so.first_name_shipping, so.last_name_shipping, so.address_shipping,
so.address_2_shipping, so.company_shipping, so.city_shipping, so.state_shipping,
so.postal_code_shipping, so.country_shipping, so.phone_shipping, so.shipping, 0, so.total,
so.phone_order, so.active, 1
from pet_old.sales_order so
left join pet_old.sales_order_promos sop
on so.id = sop.order_id;

/* There are orders without user_ids. If the email address on these rows matches an email address
   in the users table, update the user_id in the orders table to match. */

update orders o
left join users u
on o.email = u.email
set o.user_id = u.id
where o.user_id is null
and u.id is not null;

/* There are duplicate emails in the users table. Change any references in the orders table, 
   because we are about to delete the duplicate users. */
update orders set user_id = 830176 where user_id = 884338;
update orders set user_id = 781264 where user_id = 893654;
update orders set user_id = 777965 where user_id = 1078835;
update orders set user_id = 801397 where user_id = 809224;
update orders set user_id = 779114 where user_id = 866185;
update orders set user_id = 887012 where user_id = 887013;
update orders set user_id = 789557 where user_id = 902945;
update orders set user_id = 1079208 where user_id = 1079210;
update orders set user_id = 861799 where user_id = 881059;
update orders set user_id = 854226 where user_id = 87670873;
update orders set user_id = 782525 where user_id = 843818;

/* Delete duplicate records */

delete from users where id in (884338, 893654, 1078835, 809224, 866185, 887013, 902945, 
1079210, 881059, 87670873, 843818);

/* Now the email field can be unique (but still null!) */

alter table users add unique key (email);

/* Ordered products */

insert into order_products
select null, sop.order_id, p.id, sop.quantity, p.cost, 0, 0
from pet_old.sales_ordered_product sop
left join products p
on sop.product_id = p.sku;

/* Order payments */

insert into payment_types values
(1, 'Payflow', 'payflow'), (2, 'Paypal', 'paypal'), (3, 'Check', 'check');

insert into order_payments
select id, order_id, if (_child_name = 'payflowpayment', 1, if (_child_name = 'paypalpayment', 2,
if (_child_name = 'checkpayment', 3, null))) payment_type,
if (credit = 0, amount, amount * -1) amount, date
from pet_old.sales_payment_parent
where order_id != 1125545; /* Order no longer exists */

insert into order_payments_payflow
select payment_ptr_id, payment_ptr_id, cc_number, cc_expire_month, cc_expire_year,
pnref, ppref, correlation_id, cvv2match
from pet_old.sales_payflowpayment;

insert into order_payments_paypal
select payment_ptr_id, payment_ptr_id, null, correlation_id, null
from pet_old.sales_paypalpayment
where payment_ptr_id != 87927; /* Order no longer exists */

insert into order_payments_check
select payment_ptr_id, payment_ptr_id, check_number
from pet_old.sales_checkpayment;
