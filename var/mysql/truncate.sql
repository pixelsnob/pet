set foreign_key_checks = 0;

truncate orders;
truncate users;
truncate user_profiles;
truncate order_products;
truncate order_payments;
truncate order_payments_paypal;
truncate order_payments_payflow;
truncate order_payments_check;
truncate order_product_subscriptions;




insert into promos
(code, expiration, discount, extra_days)
values
('LUIS', '2013-01-01', 25, 15);

insert into promo_products values (null, 1, 182);
insert into promo_products values (null, 1, 300);
insert into promo_products values (null, 119, 182);
insert into promo_products values (null, 119, 185);

set foreign_key_checks = 1;
