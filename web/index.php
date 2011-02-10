<?php

$env = (isset($_SERVER['ENVIRONMENT']) && $_SERVER['ENVIRONMENT']) ? $_SERVER['ENVIRONMENT'] : 'prod';

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', $env, false);
sfContext::createInstance($configuration, null, 'mwContext')->dispatch();
