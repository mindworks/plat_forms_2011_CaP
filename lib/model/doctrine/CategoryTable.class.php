<?php

/**
 * CategoryTable
 *
 * origin: M
 *
 * @package    platforms
 * @subpackage model
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class CategoryTable extends Doctrine_Table
{
  public function findTopLevelCategories()
  {
    $query = $this->createQuery('c')
      ->where('c.parent_id IS NULL');

    return $query->execute();
  }

  public function addSubcategoryIdstoCategoryIds(array $categoryIds)
  {
    $subcategoryIds = $this->getAllSubcategoryIdsForCategoryIds($categoryIds);
    if(!$subcategoryIds) {
      return $categoryIds;
    }
    else {
      return array_merge($categoryIds, $this->addSubcategoryIdstoCategoryIds($subcategoryIds));
    }
  }

  public function getAllSubcategoryIdsForCategoryIds(array $categoryIds)
  {
    $query = $this->createQuery('c')
      ->select('c.id')
      ->whereIn('c.parent_id', $categoryIds);

    $result = $query->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
    if(!is_array($result)) {
      $result = array($result);
    }
    return $result;
  }

  public function translateCategoryNamesToIds(array $categoryNames)
  {
    $query = $this->createQuery('c')
      ->select('c.id')
      ->whereIn('c.name', $categoryNames);

    $result = $query->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
    if(!is_array($result)) {
      $result = array($result);
    }
    return $result;

  }
}