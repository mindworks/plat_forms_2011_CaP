<?php

/**
 * Basic security user used in mindworks projects.
 *
 * origin: RM
 *
 * @package    mwSymfonyExtraPlugin
 * @subpackage lib
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */
class mwBasicSecurityUser extends sfGuardSecurityUser
{
  /**
   * Store PRG data in session.
   *
   * @param string $key
   * @param array $data
   */
  public function storePrgData($key, array $data)
  {
    $this->setAttribute($key, $data);
  }

  /**
   * Fetch PRG data from session.
   *
   * @param string $key
   * @return array|null PRG data
   */
  public function retrievePrgData($key)
  {
    return $this->getAttribute($key);
  }

  /**
   * Remove PRG data from session
   *
   * @param string $key
   */
  public function invalidatePrgData($key)
  {
    $this->setAttribute($key, null);
  }
}