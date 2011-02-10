<?php

/**
 * ConferenceTable
 *
 * origin: M
 *
 * @package    platforms
 * @subpackage model
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class ConferenceTable extends Doctrine_Table
{
	public function findByCategoryId($categoryId)
  {
    $collectedCategoryIds = Doctrine_Core::getTable('Category')->addSubcategoryIdstoCategoryIds(array($categoryId));

    $query = $this->createQuery('c')
      ->innerJoin('c.ConferenceCategory cc')
      ->whereIn('cc.category_id', $collectedCategoryIds);

    return $query->execute();
  }

}