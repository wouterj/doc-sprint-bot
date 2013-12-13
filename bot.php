<?php

require_once __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Wj\DocBot\Console;

define('ROOT_DIR', __DIR__);

$app = new Application();
$app->add(new Console\ActivateCommand());

$app->run();
