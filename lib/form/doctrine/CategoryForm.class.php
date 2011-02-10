<?php

/**
 * Category form.
 *
 * origin: GM
 *
 * @package    platforms
 * @subpackage form
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class CategoryForm extends BaseCategoryForm
{
  public function configure()
  {

    unset(
      //$this['parent_id'],
      $this['conferences_list']
    );

  }
}
