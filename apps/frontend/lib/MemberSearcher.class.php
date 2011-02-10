<?php

/**
 * MemberSearcher
 *
 * origin: M
 *
 * @package    platforms
 * @subpackage lib
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */
class MemberSearcher
{
	private $params;
	private $query;
	private $count;

	public function search(array $params, $page = 0, $amount = 25, $withCount = false, $userId = null)
	{
		$conferenceTable = sfContext::getInstance()->getDoctrineTable('sfGuardUser');

		$this->params    = $params;
		
		$this->query     = $conferenceTable->createQuery('g');
    $this->query->where('1');
    
    //$this->addFulltextSearchCondition();
    
    $this->addNameCondition($userId);
    
    $this->addMyTownCondition();
    
    $this->addMyCountryCondition();

    
    $this->query->orderBy('g.username ASC');
    
    $this->query->offset($page * $amount);
    $this->query->limit($amount);
	    
    if ($withCount) { 
      $this->count = $this->query->count();
    }
    else {
    	$this->count = 0;
    }
    
    return $this->query->execute();

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

	
	private function addNameCondition($userId)
  {
  	if (isset($this->params['term']) && $this->params['term']) {
      if (!$userId) {
    	  $this->query->andWhere("g.username = ?", array($this->params['term']));
      }
      else {
      	$this->query->leftJoin("g.Contacts c ON g.id = c.sender_id AND c.receiver_id = ? AND c.status = 'in_contact'", array($userId));
      	$this->query->andWhere("IF (c.status = 'in_contact', s.fullname, s.username) LIKE ? OR g.username = ?", array('%'.$this->params['term'].'%', $this->params['term']));
      }
    }
  }
	
	private function addMyTownCondition()
	{
		if (isset($this->params['my_town']) && $this->params['my_town']) {
			$this->query->andWhere("g.town = ?", array($this->params['my_town']));
		}
	}
	
  private function addMyCountryCondition()
  {
    if (isset($this->params['my_country']) && $this->params['my_country']) {
      $this->query->andWhere("g.country = ?", array($this->params['my_country']));
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
      
      $this->query->innerJoin('g.Fulltext s2');
      $this->query->andWhere("s2.model = 'Member'");
      $this->query->andWhere("MATCH (s2.body) AGAINST (? ".$boolean.") ", array($term));
    
    }
	}
	
}