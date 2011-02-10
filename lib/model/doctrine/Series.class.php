<?php

/**
 * Series
 *
 * origin: GM
 *
 * @package    platforms
 * @subpackage model
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Series extends BaseSeries
{
  public function formatForApi()
  {
    $data = new StdClass();

    $data->id   = $this->getId();
    $data->name = $this->getName();

    $contacts   = array();

    foreach ($this->getContacts() as $aContact) {
      $contacts[] = $aContact->formatForApi();
    }

    $data->contacts = $contacts;

    return $data;
  }
}