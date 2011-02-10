<?php

/**
 * Abstract base test case for symfony actions tests.
 *
 * origin: RM
 *
 * @package    mwPhpUnitPlugin
 * @subpackage testcases
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: mwActionsControllerTestCase.php 798 2010-11-23 08:23:27Z mi_basedow $
 */
abstract class mwActionsControllerTestCase extends mwComponentControllerTestCase
{
  /**
   * Prepares the environment before running a test.
   */
  protected function setUp()
  {
    parent::setUp();

    $configCache = $this->getMockWithoutCallingConstructor('sfConfigCache');
    $this->context->expects($this->any())->method('getConfigCache')->will($this->returnValue($configCache));
  }
}