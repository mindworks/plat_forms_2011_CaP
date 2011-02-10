<?php

/**
 * Platform static utils class
 *
 * origin: M
 *
 * @package    platforms
 * @subpackage lib
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */
class PlatformUtils
{
  static public function getCountryMap()
  {
    return array(
      'DE' => 'Germany',
      'CH' => 'Switzerland',
      'ES' => 'Spain',
      'IT' => 'Italy',
      'PT' => 'Portugal',
      'NL' => 'Netherlands',
      'FR' => 'France',
      'UK' => 'UK',
      'US' => 'United States',
      'CA' => 'Canada',
      'CN' => 'China',
      'JP' => 'Japan',
      'IN' => 'India',
      'SG' => 'Singapore',
      'SA' => 'South Africa',
    );
  }

  static public function codeToCountry($code)
  {
    $code = strtoupper($code);
    $map  = self::getCountryMap();
    if(isset($map[$code])) {
      return $map[$code];
    }
    else {
      return $code;
    }
  }

  static public function countryToCode($countryString) {
    $map = array(
      'Germany' => 'DE',
      'Deutschland' => 'DE',
      'Switzerland' => 'CH',
      'Spain' => 'ES',
      'Italy' => 'IT',
      'Portugal' => 'PT',
      'Netherlands' => 'NL',
      'the Netherlands' => 'NL',
      'France' => 'FR',
      'UK' => 'UK',
      'Great Britain' => 'UK',
      'United States' => 'US',
      'USA' => 'US',
      'Texas 78205 USA' => 'US',
      'California USA' => 'US',
      'Canada' => 'CA',
      'China' => 'CN',
      'Japan' => 'JP',
      'India' => 'IN',
      'Singapore' => 'SG',
      'South Africa' => 'SA',
    );
    if(!isset($map[$countryString])) {
      $map[$countryString] = 'DE';
    }
    return $map[$countryString];
  }

  // example: $from = array(53.57673, 10.06448)
  static public function latLonDist(array $from, array $to, $precision = 0)
  {
    $pi       = 3.1415926;
    $c        = 57.29578;
    $mFact    = 1609.344;
    $lat1     = $from[0];
    $lon1     = $from[1];
    $lat2     = $to[0];
    $lon2     = $to[1];
    $distance = (
      3958
      * $pi
      * sqrt(
        ($lat2 - $lat1) * ($lat2 - $lat1)
        + cos($lat2 / $c) * cos($lat1 / $c) * ($lon2 - $lon1) * ($lon2 - $lon1)
      )
      / 180
    );
    return round($distance * $mFact, $precision); // meters
  }

  static public function latCalc($lat, $addMeters)
  {
    return round($lat + ((100 / 11117369) * $addMeters), 6);
  }
  static public function lonCalc($lat, $lon, $addMeters)
  {
    $c = cos($lat / 57.29578) * 111173.693725;
    return round($lon + ((1 / $c) * $addMeters), 6);
  }

  static public function splitGps($gpsString) {
    $parts = preg_split('/,\s*/', $gpsString);
    if(count($parts) < 2) {
      return array(
        'latitude' => null,
        'longitude' => null,
      );
    }
    $gpsArray = array(
     'latitude' => (float)$parts[0],
     'longitude' => (float)$parts[1],
    );
    if(strpos($gpsString, 'S') !== false) {
      $gpsArray['latitude'] *= -1.0;
    }
    if(strpos($gpsString, 'W') !== false) {
      $gpsArray['longitude'] *= -1.0;
    }
    return $gpsArray;
  }

  static public function isoToDate($date)
  {
    $timestamp = strtotime($date);
    return date('Y-m-d', $timestamp);
  }
}