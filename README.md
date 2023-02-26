<p align="center">
    <h1 align="center">Тестовое задание на вакансию PHP-разработчика</h1>
</p>

Выполнено на шаблоне basic фреймворка Yii2.

Установка
------------

~~~
git clone https://github.com/Araused/etagi-test.git
~~~

~~~
composer install
~~~

Затем создать БД MySQL, при необходимости имя базы данных и параметры подключения
можно изменить в файле `config/db.php`. После создания базы данных, выполнить

~~~
php yii migrate
~~~

После этого приложение можно запустить командой

~~~
php yii serve
~~~

Приложение будет доступно по адресу: `http://localhost:8080/`

Пользователей можно создавать при помощи консольной команды

~~~
php yii user/create-admin userName user@email.com userPassword
~~~

Более подробно: `commands/UserController.php`