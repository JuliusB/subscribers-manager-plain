<?php

use MailerTiny\Core\Application;

require __DIR__.'/../vendor/autoload.php';
$config = require __DIR__.'/../configs/database.php';

Application::start($config);

