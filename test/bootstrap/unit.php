<?php
/**
 * Bootstrap for unit tests
 *
 * origin: RM
 *
 * @package    platforms
 * @subpackage test
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */

error_reporting(E_ALL | E_STRICT);
ini_set('date.timezone', 'Europe/Berlin');

$_test_dir = realpath(dirname(__FILE__) . '/..');
set_include_path(realpath(dirname(__FILE__).'/../../').PATH_SEPARATOR.get_include_path());

// configuration
require_once dirname(__FILE__) . '/../../config/ProjectConfiguration.class.php';
$configuration = ProjectConfiguration::hasActive() ?
  ProjectConfiguration::getActive() :
  new ProjectConfiguration(realpath($_test_dir . '/..'));

// autoloader
$autoload = sfSimpleAutoload::getInstance(sfConfig::get('sf_cache_dir') . '/project_autoload.cache');
$autoload->loadConfiguration(
    sfFinder::type('file')->name('autoload.yml')->in(
      array(sfConfig::get('sf_symfony_lib_dir') . '/config/config', sfConfig::get('sf_config_dir'))));
$autoload->register();