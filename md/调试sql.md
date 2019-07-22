 ##laravel调试sql语句
 $builder = Product::query()->where('on_sale', true);      
dd($builder->toSql());

~~~mysql
select * from `products` 
where `on_sale` = ?
 and (`title` like ? or `description` like ? 
      or exists (
          select * from `product_skus`
           where `products`.`id` = `product_skus`.`product_id` 
           and (`title` like ? or `description` like ?)
      )
 )
~~~

~~~mysql
select * from `products` 
where `on_sale` = ? 
and `title` like ? or `description` like ? 
or exists (
    select * from `product_skus` 
    where `products`.`id` = `product_skus`.`product_id` 
    and (`title` like ? or `description` like ?)
    )
~~~