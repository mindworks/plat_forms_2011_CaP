<?php

/**
 * Mock class for Doctrine_Table class
 *
 * origin: RM
 *
 * @package    mwPhpUnitPlugin
 * @subpackage testcases
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */
class mwTableMock
{
  private $callMocks = array();

  public function registerCallMock($methodName, $returnValue)
  {
    $this->callMocks[$methodName] = $returnValue;
  }

  public function __call($methodName, array $args)
  {
    $match = false;
    if(isset($this->callMocks[$methodName]))
    {
      return $this->callMocks[$methodName];
    }
    else {
      return false;
    }
  }
}