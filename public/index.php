<?php

use MailerTiny\Core\Application;

require __DIR__.'/../vendor/autoload.php';
$config = require __DIR__.'/../configs/database.php';

ini_set('display_errors', 1);
Application::start($config);

