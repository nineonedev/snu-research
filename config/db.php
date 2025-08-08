<?php

use app\core\Config;

if(Config::isProduction())
{
    Config::set('db_driver', 'mysql');
    Config::set('db_host', '127.0.0.1');
    Config::set('db_name', 'dbusnures0415');
    Config::set('db_user', 'dbusnures0415');
    Config::set('db_pass', '!#DB0415snurs!');
    Config::set('db_port', 3306);
    Config::set('db_char', 'utf8mb4');
}

if(Config::isDevelopment())
{
    Config::set('db_driver', 'mysql');
    Config::set('db_host', 'db');
    Config::set('db_name', 'nineonelabs');
    Config::set('db_user', 'user');
    Config::set('db_pass', 'password');
    Config::set('db_port', 3306);
    Config::set('db_char', 'utf8mb4');
}

