select o.id as order_id, o.total, o.discount, op.cost, op.discount
from orders o
left join order_products op
on o.id = op.order_id
where o.id <= 1137067;


drop table if exists temp_orders;

create table temp_orders (
    order_id int not null unique,
    ct int,
    total decimal(5, 2),
    discount decimal(5, 2),
    shipping decimal(5, 2)
);

insert into temp_orders (
    select o.id, sum(if (op.id is null, 0, 1)) as op_count,
    o.total, o.discount, o.shipping
    from orders o
    left join order_products op
    on o.id = op.order_id
    group by o.id
);

update order_products op
set cost = (
    select round(((total + discount - shipping) / ct) / qty, 2)
    from temp_orders
    where order_id = op.order_id 
)
where op.order_id <= 1137067;



select o.id, sum(if (op.id is null, 0, 1)) as op_count,
o.total, o.discount, o.shipping, op.qty
from orders o
left join order_products op
on o.id = op.order_id
group by o.id
having op_count > 1;