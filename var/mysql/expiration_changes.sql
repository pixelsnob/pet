
alter table users add expiration date;
alter table users add previous_expiration date;
alter table users add digital_only int(1) not null default 0;
alter table users add index(expiration);
alter table users add index(digital_only);

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

update users u
set digital_only = (
    select digital_only from order_product_subscriptions
    where user_id = u.id
    and expiration = (
        select max(expiration)
        from order_product_subscriptions
        where user_id = u.id
    )
    group by user_id
);

/*drop table order_product_subscriptions;*/
