<?php

/**
 * Conference
 *
 * origin: GM
 *
 * @package    platforms
 * @subpackage model
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Conference extends BaseConference
{
  public function postSave($event)
  {
    // manage fulltext index
    if ($this->getId()) {

      $searchIndexTable = Doctrine_Core::getTable('SearchIndex');
      $fulltext         = $searchIndexTable->findOneByModelAndObject_id('Conference', $this->getId());
      if (!$fulltext instanceof SearchIndex) {
        $fulltext            = new SearchIndex();
        $fulltext->model     = 'Conference';
        $fulltext->object_id = $this->getId();
      }

      $body  = '';
      $body .= $this->getName().' ';
      $body .= $this->getDescription().' ';
      $body .= $this->getVenue().' ';
      $body .= $this->getAccomodation().' ';
      $body .= $this->getHowtofind().' ';
      $body .= $this->getLocation().' ';

      $fulltext->body = $body;
      $fulltext->save();
    }

    parent::postSave($event);
  }

  // Creates a json-representation of this object.
  public function formatForApi()
  {
    $data = new StdClass();

    $data->name = $this->getName();

    if ($this->getCreator()->exists()) {
      $data->creator = $this->getCreator()->formatForApi();
    }
    else {
      $data->creator = null;
    }

    if ($this->getSeries()->exists()) {
      $data->series = $this->getSeries()->formatForApi();
    }

    $data->startdate    = $this->getStartdate();
    $data->enddate      = $this->getEnddate();
    $data->categories   = $this->getCategoryNamesAsArray();
    $data->description  = $this->getDescription();
    $data->location     = $this->getLocation();
    $data->gps          = sprintf('%s,%s', $this->latitude, $this->longitude);
    $data->venue        = $this->getVenue();
    $data->accomodation = $this->getAccomodation();
    $data->howtofind    = $this->getHowtofind();

    return $data;
  }

  public function getCategoryNamesAsArray()
  {
    $categories = array();

    foreach ($this->getCategories() as $aCategory) {
      $categories[] = $aCategory->getName();
    }

    return $categories;
  }

  public function preSave($event)
  {
    parent::preSave($event);

    $locationParts = $this->splitLocationString($this->location);
    $this->country = $locationParts['country'];
  }

  public function setGpsCoordinates($gps)
  {
    $gpsParts = PlatformUtils::splitGps($gps);
    $this->setLatitude($gpsParts['latitude']);
    $this->setLongitude($gpsParts['longitude']);
  }

  public function splitLocationString($location)
  {
    $locationArray = array(
      'country' => 'DE',
      'town' => '',
    );
    $parts = preg_split('/,\s*/', $location);
    $numParts = count($parts);
    switch($numParts) {
      case 4:
        $locationArray['town'] = $parts[1];
        $locationArray['country'] = PlatformUtils::countryToCode($parts[3]);
      break;
      case 3:
        $locationArray['town'] = $parts[1];
        $locationArray['country'] = PlatformUtils::countryToCode($parts[2]);
      break;
      case 2:
        $locationArray['town'] = $parts[0];
        $locationArray['country'] = PlatformUtils::countryToCode($parts[1]);
      break;
      case 1:
        $locationArray['town'] = $parts[0];
      break;
    }
    return $locationArray;
  }
}