<?php

require_once 'plugins/mwPhpUnitPlugin/lib/mocks/mwDoctrineRecordMock.php';

/**
 * mock class for Doctrine_Collection class
 *
 * origin: RM
 *
 * @package    mwPhpUnitPlugin
 * @subpackage testcases
 * @author     Melwin Meyer <mmeyer@mindworks.de
 * @version    SVN: $Id: mwDoctrineCollectionMock.php 1053 2010-12-16 14:47:13Z mi_mmeyer $
 */
class mwDoctrineCollectionMock implements Countable, Iterator, ArrayAccess
{
  /**
   * @var array Array of mwDoctrineRecordMock
   */
  public $collectionArray = array();

  /**
   * @var integer
   */
  private $pos = 0;

  /**
   * Internal deleted state
   *
   * @var boolean
   */
  private $isDeleted = false;

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
   * Set internal deleted state to true.
   * @return boolean true on success, false on failure
   */
  public function delete()
  {
    $this->isDeleted = true;
    return $this->isDeleted;
  }

  /**
   * @param mwDoctrineRecordMock $object
   * @return boolean
   */
  public function add(mwDoctrineRecordMock $object)
  {
    $this->collectionArray[] = $object;
    return true;
  }

  /**
   * @return integer
   */
  public function count()
  {
    return count($this->collectionArray);
  }

  /**
   * @return mwDoctrineRecordMock
   */
  public function end()
  {
    return end($this->collectionArray);
  }

  /**
   * @return mwDoctrineRecordMock
   */
  public function current()
  {
    return $this->collectionArray[$this->pos];
  }

  /**
   *
   */
  public function next()
  {
    ++$this->pos;
  }

  /**
   * @return integer
   */
  public function key()
  {
    return $this->pos;
  }

  /**
   * @return boolean
   */
  public function valid()
  {
    return isset($this->collectionArray[$this->pos]);
  }

  /**
   *
   */
  public function rewind()
  {
    $this->pos = 0;
  }

  /**
   * Get the first record in the collection.
   */
  public function getFirst()
  {
    return $this->collectionArray[0];
  }

/**
 * @param integer $offset
 */
  public function offsetExists($offset)
  {
    return isset($this->collectionArray[$offset]);
  }

/**
 * @param integer $offset
 */
  public function offsetGet($offset)
  {
    return $this->collectionArray[$offset];
  }

/**
 * @param integer $offset
 * @param mixed $value
 */
  public function offsetSet($offset, $value)
  {
    $this->collectionArray[$offset] = $value;
  }

/**
 * @param integer $offset
 */
  public function offsetUnset($offset)
  {
    unset($this->collectionArray[$offset]);
  }

}