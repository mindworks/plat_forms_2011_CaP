<?php

/**
 * PlatformsDataImporter
 *
 * Import default data from json file into the database.
 *
 * origin: M
 *
 * @package    platforms
 * @subpackage lib
 * @version    SVN: $Id: $
 */
class PlatformsDataImporter
{
  private $context;

  public function __construct(mwContext $context)
  {
    $this->context = $context;
  }

  public function importAll($jsonString)
  {
    $dataArry = json_decode($jsonString, true);
    $lastError = json_last_error();
    switch($lastError)
    {
        case JSON_ERROR_DEPTH:
            $error = 'Maximum stack depth exceeded';
        break;
        case JSON_ERROR_CTRL_CHAR:
            $error = 'Unexpected control character found';
        break;
        case JSON_ERROR_SYNTAX:
            $error = 'Syntax error, malformed JSON';
        break;
        default:
          $error = false;
    }
    if($error) {
      throw new RuntimeException('JSON Error: '.$error);
    }
    $namedCollections = $this->splitToEntityCollections($dataArry);
    foreach($namedCollections['categories'] as $categoryData) {
      $this->importCategory($categoryData);
    }
    foreach($namedCollections['users'] as $userData) {
      $this->importUser($userData);
    }
    foreach($namedCollections['series'] as $seriesData) {
      $this->importSeries($seriesData);
    }
    foreach($namedCollections['conferences'] as $conferenceData) {
      $this->importConference($conferenceData);
    }
  }

  public function splitToEntityCollections(array $allCollection)
  {
    return array(
      "users" => $allCollection[0],
      "categories" => $allCollection[1],
      "series" => $allCollection[2],
      "conferences" => $allCollection[3],
    );
  }

  public function importUser(array $userData)
  {
    $user = $this->getDoctrineTable('sfGuardUser')->findOneByUsername($userData['username']);
    if(!$user) {
      $user = $this->createModel('sfGuardUser');
      $user->setUsername($userData['username']);
      $user->setPassword($userData['password']);
      $user->setFullname($userData['fullname']);
      $user->setEmailAddress($userData['email']);
      $user->setTown($userData['town']);
      $user->setCountry(PlatformUtils::countryToCode($userData['country']));

      $gpsParts = PlatformUtils::splitGps($userData['gps']);
      $user->setLatitude($gpsParts['latitude']);
      $user->setLongitude($gpsParts['longitude']);

      $user->save();
    }
    return $user;
  }

  public function importCategory(array $categoryData)
  {
    $category = $this->getDoctrineTable('Category')->findOneByName($categoryData['name']);
    if(!$category) {
      $category = $this->createModel('Category');
      $category->setName($categoryData['name']);
      if(isset($categoryData['parent']['name'])) {
        $parent = $this->importCategory($categoryData['parent']);
        $category->setParent($parent);
      }
      $category->save();
    }
    return $category;
  }

  public function importSeries(array $seriesData)
  {
    $series = $this->createModel('Series');
    $series->setName($seriesData['name']);

    foreach($seriesData['contacts'] as $contactData) {
      $contact = $this->getDoctrineTable('sfGuardUser')->findOneByUsername($contactData['username']);
      if($contact) {
        $series->getContacts()->add($contact);
      }
    }
    $series->save();
    return $series;
  }

  public function importConference(array $conferenceData)
  {
    $conference = $this->createModel('Conference');
    $conference->setName($conferenceData['name']);
    $conference->setStartDate(PlatformUtils::isoToDate($conferenceData['startdate']));
    $conference->setEndDate(PlatformUtils::isoToDate($conferenceData['enddate']));

    if(isset($conferenceData['series']['name'])) {
      $series = $this->getDoctrineTable('Series')->findOneByName($conferenceData['series']['name']);
      $conference->setSeries($series);
    }

    if(isset($conferenceData['description'])) {
      $conference->setDescription($conferenceData['description']);
    }

    if(isset($conferenceData['venue'])) {
      $conference->setVenue($conferenceData['venue']);
    }

    if(isset($conferenceData['accomodation'])) {
      $conference->setAccomodation($conferenceData['accomodation']);
    }

    if(isset($conferenceData['howtofind'])) {
      $conference->setHowtofind($conferenceData['howtofind']);
    }

    foreach($conferenceData['categories'] as $categoryData) {
      $category = $this->getDoctrineTable('Category')->findOneByName($categoryData['name']);
      if($category) {
        $conference->getCategories()->add($category);
      }
    }

    $creator = $this->getDoctrineTable('sfGuardUser')->findOneByUsername($conferenceData['creator']['username']);
    if($creator) {
      $conference->setCreator($creator);
    }

    $conference->setLocation($conferenceData['location']);

    $conference->setGpsCoordinates($conferenceData['gps']);

    $conference->save();
    return $conference;
  }

  private function createModel($modelName)
  {
    return $this->context->createModel($modelName);
  }

  /**
   * Convenience method to fetch table class for given model name.
   *
   * @param string $modelName
   * @return Doctrine_Table
   */
  private function getDoctrineTable($modelName)
  {
    return $this->context->getDoctrineTable($modelName);
  }
}