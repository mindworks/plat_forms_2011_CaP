<?php
/**
 * Mock class for Doctrine_Record class
 *
 * origin: RM
 *
 * @package    mwPhpUnitPlugin
 * @subpackage testcases
 * @author     Melwin Meyer <mmeyer@mindworks.de
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: mwDoctrineRecordMock.php 1051 2010-12-16 14:02:15Z mi_mmeyer $
 */
class mwDoctrineRecordMock
{
  /**
   * Internal deleted state
   *
   * @var boolean
   */
  private $isDeleted = false;

  /**
   * Internal valid state
   *
   * @var boolean
   */
  private $isValid = true;

  /**
   * Internal save state
   *
   * @var boolean
   */
  private $isSaved = false;

  /**
   * Internal data holder
   *
   * @var array
   */
  protected $data = array();

  /**
   * Stores relations of unlink calls.
   *
   * @var array
   */
  protected $unlinkCallRelations = array();


  /**
   * Mocked Doctrine_Table
   *
   * @var Doctrine_Table
   */
  protected $table = null;

  /**
   * Constructor
   *
   * @param array $data
   */
  public function __construct(array $data = array())
  {
    $this->data = $data;
  }

  /**
   * Get Id from internal data holder, default 1.
   *
   * @return mixed
   */
  public function getId()
  {
    return $this->getDataSettingDefault('Id', 1);
  }

  /**
   * Generate key to store temp data for rpg pattern, so that it is not mixed
   * with other models.
   *
   * @return string
   */
  public function getUniqueKey()
  {
    return get_class($this);
  }

  /**
   * Set value for key from internal data holder.
   *
   * @return mixed
   */
  public function get($key)
  {
    return isset($this->data[$key]) ? $this->data[$key] : null;
  }

  /**
   * Set value for key in internal data holder.
   *
   * @param string $key
   * @param mixed $value
   */
  public function set($key, $value)
  {
    return $this->data[$key] = $value;
  }

  /**
   * route getXx and setXx calls to get() and set() methods respectively.
   *
   * @todo Internaly use "doctrine" internal names (lcfirst and uncamelcase)
   *
   * @param atring $methodName
   * @param array $args
   */
  public function __call($methodName, array $args)
  {
    $match = false;
    if(preg_match('/^(get|set)(\w+)/', $methodName, $match))
    {
      $attributeName = $match[2];
      if('get' == $match[1]){
        return $this->get($attributeName);
      }
      else {
        $this->set($attributeName, $args[0]);
      }
    }
    else {
      throw new RuntimeException('Call to undefined method '.get_class($this).'::'.$methodName.'()');
    }
  }

  /**
   * Set internal deleted state to true.
   * @return boolean true on success, false on failure
   */
  public function delete()
  {
    $this->isDeleted = true;
    return $this->isDeleted;
  }

  /**
   * Get internal deleted state.
   *
   * @return boolean
   */
  public function isDeleted()
  {
    return $this->isDeleted;
  }

  /**
   * Set internal saved state to true.
   */
  public function save()
  {
    $this->isSaved = true;
  }

  /**
   * Get internal saved state.
   *
   * @return boolean
   */
  public function isSaved()
  {
    return $this->isSaved;
  }

  /**
   * Set internal saved state to given value, default true.
   *
   * @param boolean $value
   */
  public function setValid($value = true)
  {
    $this->isValid = $value;
  }

  /**
   * Get internal valid state.
   *
   * @return boolean
   */
  public function isValid()
  {
    return $this->isValid;
  }

  /**
   * Get data for key. If no data set for key set default for key and return it.
   *
   * @param string $key
   * @param mixed $default
   * @return mixed
   */
  protected function getDataSettingDefault($key, $default)
  {
    if(!isset($this->data[$key]))
    {
      $this->data[$key] = $default;
    }
    return $this->data[$key];
  }

  /**
   * Mocks unlink relation calls
   *
   * @param string $relation
   */
  public function unlink($relation)
  {
    $this->unlinkCallRelations[] = $relation;
  }

  /**
   * Get relations of unlink calls.
   *
   * @retrun array
   */
  public function getUnlinkCallRelations()
  {
    return $this->unlinkCallRelations;
  }

  /**
   * Get Data holder
   *
   * @return array
   */
  public function toArray()
  {
    return $this->data;
  }

   /**
   * Set Doctrine_Table mock
   *
   * @param $table Doctrine_Table
   */
  public function setTable($table)
  {
    $this->table = $table;
  }

  /**
   * Get Doctrine_Table mock
   *
   * @return Doctrine_Table
   */
  public function getTable()
  {
    if(is_null($this->table))
    {
      throw new RuntimeException('Please inject table mock in test set up using mwDoctrineRecordMock::setTable().');
    }
    return $this->table;
  }
}