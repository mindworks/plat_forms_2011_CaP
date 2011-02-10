<?php

/**
 * categories actions.
 *
 * origin: GM
 *
 * @package    platforms
 * @subpackage categories
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */
class categoriesActions extends mwActions
{
  /**
   * Executes index action
   *
   * @param sfRequest $request A request object
   */
  public function executeIndex(sfWebRequest $request)
  {

    $this->categories  = $this->getDoctrineTable('Category')->findTopLevelCategories();

    $conferenceSearcher = new ConferenceSearcher();

    $params                    = array();
    $conferences               = $conferenceSearcher->search($params, 0, 100, true);
    $this->conferences         = $conferences;
    $this->count               = $conferenceSearcher->getCount();

    $params                    = array(
      'date_start' => date('Y-m-d'),
      'date_end'   => date('Y-m-d'),
    );
    $conferences               = $conferenceSearcher->search($params, 0, 25, false);
    $this->conferencesToday    = $conferences;

    $params                    = array(
      'date_start'       => date('Y-m-d', time() + 86400),
      'date_start_exact' => true,
    );
    $conferences               = $conferenceSearcher->search($params, 0, 25, false);
    $this->conferencesTomorrow = $conferences;

    if ($this->isApiCall) {
      $this->getResponse()->setStatusCode(200);
    }
  }

  public function executeShowCategory(sfWebRequest $request)
  {
    $categoryId = $request->getParameter('id');
    $this->selectedCategory = $this->getDoctrineTable('Category')->find($categoryId);

    $this->conferences = $this->getDoctrineTable('Conference')->findByCategoryId($categoryId);

    $this->forward404Unless($this->selectedCategory);
  }

  // -------------------

  public function executeEditCategory(sfWebRequest $request)
  {
    $user = $this->getUser();

    if ($request->getParameter('start')) {
      $user->invalidatePrgData('category');
    }

    $incomingData = $user->retrievePrgData('category');

    $id           = $request->getParameter('id');

    $category = null;
    if ($id) {
      $category = $this->getDoctrineTable('category')->find($id);
    }
    if (!$category instanceof Category) {
      $category = $this->createModel('Category');
    }

    $editForm = new CategoryForm($category);

    if ($incomingData && is_array($incomingData)) {
      $editForm->bind($incomingData);
    }

    // ------------------

    $this->editForm   = $editForm;

    $this->category = $category;
  }

  public function executeSaveCategory(sfWebRequest $request)
  {
    $user = $this->getUser();

    $incomingData = $request->getParameter('category', array());
    $id           = $incomingData['id'];
    if (!$id) {
      $id = 0;
    }

    $category = null;
    if ($id) {
      $category = $this->getDoctrineTable('Category')->find($id);
    }
    if (!$category instanceof Category) {
      $id       = 0;
      $category = $this->createModel('Category');
    }

    $editForm     = new CategoryForm($category);
    $editForm->bind($incomingData);


    $valid = $editForm->isValid();
    if (!$valid) {

      $user->storePrgData('category', $incomingData);

      $this->redirect('@category_edit?id='.$id, 303);
    }
    else {

      $editForm->save();

      $id = $category->getId();

      $user->invalidatePrgData('category');

      $this->redirect('@category_show?id='.$id, 303);
    }

  }

  protected function prepareCategories()
  {
    $query = $this->getDoctrineTable('Category')->createQuery('a');

    if ($this->selectedCategory) {
      $query->where('parent_id = ?', $this->selectedCategory->getId());
    }
    else {
      $query->where('parent_id IS NULL');
    }

    $query->orderBy('name ASC');

    $this->categories = $query->execute();
  }

//  protected function prepareFutureConferences()
//  {
//    $query = $this->getDoctrineTable('Conference')->createQuery('a');
//
//    $startingTheDayAfterTomorrow = mktime(0, 0, 0, date("n"), date("j") + 2, date("Y"));
//
//    $query->where('start >= ?', date('YYYY-mm-dd', $startingTheDayAfterTomorrow));
//
//    $this->futureConferences = $query->execute();
//  }
}
