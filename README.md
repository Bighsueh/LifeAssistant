Heroku Webhook URL
https://laravel-line-assistant.herokuapp.com/api/callback

--------------------

安裝套件
```
composer require linecorp/line-bot-sdk
```
官方文件
https://packagist.org/packages/linecorp/line-bot-sdk


缺少ext-sockets: *的話要去php.ini 將
```
extension=php_sockets.dll
```
取消註解，沒有這行的話要自行加上去或自行至官網下載https://www.php.net/downloads.php


參考資料：
StackOverFlow:
https://stackoverflow.com/questions/1361925/how-to-enable-socket-in-php

--------------------

Heroku CLI
https://devcenter.heroku.com/articles/heroku-cli#download-and-install

建好route要記得run一下確定自己有註冊成功
```
php artisan route:list
```

--------------------
Coding中遇到的Bug串(已解決)：

若Laravel.log 遇到 Invalid signature has given 
-> 抓不到channel_secret
