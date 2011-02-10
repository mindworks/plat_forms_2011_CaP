<?php

require_once dirname(__FILE__).'/../lib/vendor/symfony/lib/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();

class ProjectConfiguration extends sfProjectConfiguration
{
  public function setup()
  {
    $this->enablePlugins(
      'sfDoctrinePlugin',
      'mwSymfonyExtraPlugin',
      'mwPhpUnitPlugin',
      'sfDoctrineGuardPlugin',
      'sfiCalCreatorPlugin'
    );
  }

  /**
   * Is automatically called when database usage and docrtine pugin is enabled
   *
   * @param Doctrine_Manager $manager
   */
  public function configureDoctrine(Doctrine_Manager $manager)
  {
    $manager->setAttribute(Doctrine::ATTR_VALIDATE, Doctrine::VALIDATE_ALL);

    $manager->setCollate('utf8_unicode_ci');
    $manager->setCharset('utf8');

//    $manager->setAttribute(Doctrine::ATTR_QUOTE_IDENTIFIER, true);
//    $manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);

    $options = array(
      'baseClassName'        => 'mwDoctrineRecord',
      'generateTableClasses' => false,
      'phpDocName'           => 'Joerg Basedow',
      'phpDocEmail'          => 'jbasedow@mindworks.de'
    );
    sfConfig::set('doctrine_model_builder_options', $options);
  }
}