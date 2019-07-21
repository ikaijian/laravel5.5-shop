##管理后台

###安装 laravel-admin 扩展包
####encore/laravel-admin 扩展包
>encore/laravel-admin 是一个可以快速构建后台管理的扩展包，它提供了页面组件和表单元素等功能，
只需要使用很少的代码就实现功能完善的后台管理功能

安装
~~~
composer require encore/laravel-admin "1.5.*"
~~~
发布
~~~
php artisan vendor:publish --provider="Encore\Admin\AdminServiceProvider"
php artisan admin:install
~~~
第一个命令会将 Laravel-Admin 的一些文件发布到我们项目目录中，比如前端 JS/CSS 文件、配置文件等
第二个命令是执行数据库迁移、创建默认管理员账号、默认菜单、默认权限以及创建一些必要的目录



###laravel-admin创建控制器
~~~
php artisan admin:make UsersController --model=App\Models\User
~~~
其中 --model=App\\Models\\User 代表新创建的这个控制器是要对 App\Models\User 这个模型做增删改查

