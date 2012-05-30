truncate orders;
truncate users;
truncate user_profiles;
truncate ordered_products;
truncate order_payments;
truncate order_payments_paypal;
truncate order_payments_payflow;
truncate order_payments_check;
truncate order_subscriptions;




insert into promos
(code, expiration, discount, extra_days)
values
('LUIS', '2013-01-01', 25, 15);

insert into promo_products values (null, 1, 182);