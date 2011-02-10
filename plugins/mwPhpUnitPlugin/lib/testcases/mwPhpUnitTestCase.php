<?php

/**
 * Abstract base test case.
 *
 * origin: RM
 *
 * @package    mwPhpUnitPlugin
 * @subpackage testcases
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: mwPhpUnitTestCase.php 844 2010-11-26 14:28:12Z mi_basedow $
 */
abstract class mwPhpUnitTestCase extends PHPUnit_Framework_TestCase
{
  /**
   * Creates a mock object without calling its constructor.
   *
   * @param  string  $className
   * @param  array   $methods
   * @param  array   $arguments
   * @return object
   */
  protected function getMockWithoutCallingConstructor($className, $methods = array(), array $arguments = array())
  {
    return $this->getMock($className, $methods, $arguments, '', false);
  }

  /**
   * Gets the root directory of the symfony project under test.
   *
   * @return string
   */
  protected function getSfRootDir()
  {
    return realpath(dirname(__FILE__).'/../../../../');
  }
}