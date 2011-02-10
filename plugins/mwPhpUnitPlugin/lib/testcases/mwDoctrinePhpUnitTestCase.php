<?php

/**
 * Abstract base test case for Doctrine DB tests.
 *
 * origin: RM
 *
 * @package    mwPhpUnitPlugin
 * @subpackage testcases
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: mwDoctrinePhpUnitTestCase.php 844 2010-11-26 14:28:12Z mi_basedow $
 */
abstract class mwDoctrinePhpUnitTestCase extends mwPhpUnitTestCase
{
  /**
   * Prepares the environment before running a test.
   */
  protected function setUp()
  {
    parent::setUp();

    Doctrine_Manager::connection(new PDO('sqlite::memory:'));
    Doctrine::createTablesFromModels($this->getSfRootDir().'/lib/model');
  }

  /**
   * Cleans up the environment after running a test.
   */
  protected function tearDown()
  {
    Doctrine_Manager::getInstance()->closeConnection(Doctrine_Manager::connection());

    parent::tearDown();
  }
}