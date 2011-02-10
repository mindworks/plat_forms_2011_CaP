<?php

/**
 * ConferenceSearcher
 *
 * origin: M
 *
 * @package    platforms
 * @subpackage lib
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */
class ConferenceSearcher
{
  private $params;
  private $query;
  private $count;

  /**
   *
   * Example:
   *   array(
   *     'term'             => 'Advanced conference',
   *     'date_start_exact' => true,
   *     'date_start'       => '2010-10-10',
   *     'date_end'         => '2010-10-10',
   *     'regions'          => array(
   *       'DE', 'IT', ...
   *     ),
   *     'country_only' => 'DE',
   *     'cats' => array(
   *       10, 11, 12, ...
   *     ),
   *   )
   *
   * @param array $params
   */
  public function search(array $params, $page = 0, $amount = 100, $withCount = false, $execute = true)
  {
    $conferenceTable = sfContext::getInstance()->getDoctrineTable('Conference');

    $this->params    = $params;

    $this->query     = $conferenceTable->createQuery('c');
    $this->query->where('1');

    $this->addFulltextSearchCondition();

    $this->addDateRangeCondition();

    $this->addRegionsCondition();

    $this->addCategoriesCondition();

    $this->query->orderBy('c.startdate ASC');

    $this->query->offset($page * $amount);
    $this->query->limit($amount);

    if ($execute) {

      if ($withCount) {
        $this->count = $this->query->count();
      }
      else {
        $this->count = 0;
      }

      return $this->query->execute();
    }

    return array();
  }

  public function getQuery()
  {
    return $this->query;
  }

  public function getCount()
  {
    return $this->count;
  }

  public function getParams()
  {
    return $this->params;
  }

  private function addCategoriesCondition()
  {
    $catIds = array();
    if (isset($this->params['cats']) && $this->params['cats'] && is_array($this->params['cats'])){
      foreach ($this->params['cats'] as $catId) {
        $catId = abs((int)$catId);
        if ($catId) {
          $catIds[$catId] = $catId;
        }
      }
    }
    if ($catIds) {
      $this->query->innerJoin('c.ConferenceCategory cc');
      $this->query->andWhereIn('cc.category_id', array_values($catIds));
    }
  }

  private function addRegionsCondition()
  {
    $map     = PlatformUtils::getCountryMap();
    $regions = array();
    if (isset($this->params['regions']) && $this->params['regions'] && is_array($this->params['regions'])){
      foreach ($this->params['regions'] as $cc) {
        if (isset($map[$cc])) {
          $regions[$cc] = $cc;
        }
      }
    }
    elseif (isset($this->params['country_only']) && $this->params['country_only']) {
      $cc = $this->params['country_only'];
      if (isset($map[$cc])) {
        $regions[$cc] = $cc;
      }
    }
    if ($regions) {
      $this->query->andWhereIn('c.country', array_values($regions));
    }
  }

  private function addDateRangeCondition()
  {
    $dateFrom = false;
    $dateTo   = false;
    if (isset($this->params['date_start']) && $this->params['date_start']) {
      $dateFrom = $this->params['date_start'];
      $dateFrom = strtotime($dateFrom);
      $dateFrom = date('Y-m-d', $dateFrom);

      $this->params['date_start'] = $dateFrom;
    }
    if (isset($this->params['date_end']) && $this->params['date_end']) {
      $dateTo = $this->params['date_end'];
      $dateTo = strtotime($dateTo);
      $dateTo = date('Y-m-d', $dateTo);

      $this->params['date_end'] = $dateTo;
    }

    $startExact = false;
    if (isset($this->params['date_start_exact']) && $this->params['date_start_exact']) {
      $startExact = true;
    }

    if (!$dateFrom && !$dateTo) {
      $dateFrom = date('Y-m-d');
    }

    if ($startExact && $dateFrom) {
      $this->query->andWhere("c.startdate = ?", array($dateFrom));
    }
    else {
      if ($dateFrom) {
        $this->query->andWhere("c.enddate >= ?", array($dateFrom));
      }
      if ($dateTo) {
        $this->query->andWhere("c.startdate <= ?", array($dateTo));
      }
    }

  }

  private function addFulltextSearchCondition()
  {
    if (isset($this->params['term']) && $this->params['term']) {
      $term    = $this->params['term'];

      $boolean = '';
      $temp    = preg_split('/\s/', $term);
      if (count($temp) > 1) {
        $term    = '+'.join(' +', $temp);
        $boolean = 'IN BOOLEAN MODE';
      }
      else {
        $term = join('', $temp);
      }

      $this->query->innerJoin('c.Fulltext s');
      $this->query->andWhere("s.model = 'Conference'");
      $this->query->andWhere("MATCH (s.body) AGAINST (? ".$boolean.") ", array($term));

    }
  }

}