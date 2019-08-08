##Composer切换到国内镜像(不加-g即是当前全局)
###全局配置
~~~
composer config -g repositories.packagist composer http://packagist.phpcomposer.com
~~~

###当前项目
~~~
composer config repositories.packagist composer http://packagist.phpcomposer.com
~~~

##Composer切换到Laravel-China 镜像
###全局配置
~~~
composer config -g repo.packagist composer https://packagist.laravel-china.org
~~~

###当前项目
~~~
composer config repo.packagist composer https://packagist.laravel-china.org
~~~

###阿里云 Composer 全量镜像全局配置（推荐）
所有项目都会使用该镜像地址：
~~~
composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
~~~
取消配置：
~~~
composer config -g --unset repos.packagist
~~~


##安装与卸载包（全局需要加global）
###安装
~~~
composer require yansongda/pay
~~~

###卸载
~~~
composer remove yansongda/pay
~~~

###从composer.json文件中删除包
~~~
composer update
~~~

###composer 命令增加 -vvv 可输出详细的信息
~~~
composer -vvv require alibabacloud/sdk
~~~


