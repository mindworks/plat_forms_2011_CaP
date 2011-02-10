<?php

/**
 * series actions.
 *
 * origin: GM
 *
 * @package    platforms
 * @subpackage series
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */
class seriesActions extends mwActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $series = $this->getDoctrineTable('Series')->findAll();

    $this->series = $series;
  }

  public function executeShowSeries(sfWebRequest $request)
  {
    $id     = $request->getParameter('id');

    $series = $this->getDoctrineTable('series')->find($id);
    $this->forward404Unless($series instanceof Series);

    $this->series = $series;

    $conferences = $series->getConferences();
    $contacts    = $series->getContacts();


    $this->conferences = $conferences;
    $this->contacts    = $contacts;

  }

  // -------------------

  public function executeEditSeries(sfWebRequest $request)
  {
    $user = $this->getUser();

    if ($request->getParameter('start')) {
      $user->invalidatePrgData('series');
    }

    $incomingData = $user->retrievePrgData('series');

    $id           = $request->getParameter('id');

    $series = null;
    if ($id) {
      $series = $this->getDoctrineTable('series')->find($id);
    }
    if (!$series instanceof Series) {
      $series = $this->createModel('Series');
    }

    $editForm = new SeriesForm($series);

    if ($incomingData && is_array($incomingData)) {
      $editForm->bind($incomingData);
    }

    // ------------------

    $this->editForm   = $editForm;

    $this->series = $series;
  }

  public function executeSaveSeries(sfWebRequest $request)
  {
    $user = $this->getUser();

    $incomingData = $request->getParameter('series', array());
    $id           = $incomingData['id'];
    if (!$id) {
      $id = 0;
    }

    $series = null;
    if ($id) {
      $series = $this->getDoctrineTable('Series')->find($id);
    }
    if (!$series instanceof Series) {
      $id       = 0;
      $series = $this->createModel('Series');
    }

    $editForm     = new SeriesForm($series);
    $editForm->bind($incomingData);


    $valid = $editForm->isValid();
    if (!$valid) {

      $user->storePrgData('series', $incomingData);

      $this->redirect('@series_edit?id='.$id, 303);
    }
    else {

      $editForm->save();

      $id = $series->getId();

      $user->invalidatePrgData('series');

      $this->redirect('@series_show?id='.$id, 303);
    }

  }
}
