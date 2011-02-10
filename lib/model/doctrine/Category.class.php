<?php

/**
 * Category
 *
 * origin: GM
 *
 * @package    platforms
 * @subpackage model
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Category extends BaseCategory
{
  public function getParentRoute()
  {
    $route = '@homepage';
    if($this->getParentId() > 0) {
      $route = '@category_show?id='.$this->getParentId();
    }
    return $route;
  }

  public function getParentName()
  {
    $name = 'Main page';
    if($this->getParentId() > 0) {
      $name = $this->getParent()->getName();
    }
    return $name;
  }
}
