<?php

/**
 * Base class for model classes in mindworks projects.
 *
 * origin: RM
 *
 * @package    mwSymfonyExtraPlugin
 * @subpackage lib
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */
abstract class mwDoctrineRecord extends sfDoctrineRecord
{
  /**
   * Generate key to store temp data for PRG pattern, so that it is not mixed
   * with other models.
   *
   * @return string
   */
  public function getUniqueKey()
  {
    return get_class($this);
  }

  /**
   * Convenience method to improve readability.
   *
   * @return Doctrine_Record
   */
  public function deepCopy()
  {
    return $this->copy(true);
  }
}