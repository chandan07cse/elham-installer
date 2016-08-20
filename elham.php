#! /usr/bin/php
<?php
require "vendor/autoload.php";
use Symfony\Component\Console\Application;
use GuzzleHttp\Client as Client;
use installer\NewInstaller;
$app = new Application('Elham Installer','1.0.0');
$app->add(new NewInstaller(new Client));
$app->run();

?>