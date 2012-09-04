
alter table users add expiration date;
alter table users add previous_expiration date;

update users u
set expiration = (
    select max(expiration) from order_product_subscriptions
    where user_id = u.id
), previous_expiration = (
    select expiration from order_product_subscriptions
    where user_id = u.id
    order by expiration desc
    limit 1, 1
);

drop table order_product_subscriptions;
