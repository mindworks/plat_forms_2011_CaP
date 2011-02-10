<?php

/**
 * conferences actions.
 *
 * origin: GM
 *
 * @package    platforms
 * @subpackage conferences
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */
class conferencesActions extends mwActions
{
  protected function identifyConferenceById($with404forward = true)
  {
    $id = $this->getRequest()->getParameter('id');
    if ($id) {
      $this->conference = $this->getDoctrineTable('conference')->find($id);
      if ($with404forward) {
        $this->forward404Unless($this->conference instanceof Conference);
      }
    }
  }

  /**
   * Executes index action
   *
   * @param sfRequest $request A request object
   */
  public function executeIndex(sfWebRequest $request)
  {
    $params             = $request->getParameter('f', array());

    $conferenceSearcher = new ConferenceSearcher();

    $conferences        = $conferenceSearcher->search($params, 0, 100, true);

    $this->conferences  = $conferences;

    $this->params       = $conferenceSearcher->getParams();

    $this->count        = $conferenceSearcher->getCount();

    $this->countries    = PlatformUtils::getCountryMap();

    $this->myCountry    = '';

    // Preloading personal calendar of the current user
    if ($this->getUser()->isAuthenticated()) {
      $guardUser = $this->getUser()->getGuardUser();
      $guardUser->getDefaultCalendarItems();

      $this->myCountry = $guardUser->getCountry();
    }

    $this->categories = $this->getDoctrineTable('Category')->findTopLevelCategories();
  }

  public function executeAddToCalendar(sfWebRequest $request)
  {
    $this->identifyConferenceById();

    $this->getUser()->getGuardUser()->link('DefaultCalendarItems', array($this->conference->getId()));
    $this->getUser()->getGuardUser()->save();

    $this->setInfoFlash('Conference "'.$this->conference.'" added to your calendar.');

    $referer = $request->getReferer();

    if ($referer) {
      $this->redirect($referer);
    }
    else {
      $this->redirect($this->generateUrl('conferences'));
    }
  }

  public function executeRemoveFromCalendar(sfWebRequest $request)
  {
    $this->identifyConferenceById();

    $this->getUser()->getGuardUser()->unlink('DefaultCalendarItems', array($this->conference->getId()));
    $this->getUser()->getGuardUser()->save();

    $this->setInfoFlash('Conference "'.$this->conference.'" removed from your calendar.');

    $referer = $request->getReferer();

    if ($referer) {
      $this->redirect($referer);
    }
    else {
      $this->redirect($this->generateUrl('conferences'));
    }
  }

  public function executeInviteContacts(sfWebRequest $request)
  {
    $this->identifyConferenceById();

    $contactIds = $request->getParameter('contacts');
    if (empty($contactIds)) {
      $this->setErrorFlash('No contacts provided.');
      $this->redirect($this->generateUrl('conference_show', array('id' => $this->conference->getId())));
    }

    $user      = $this->getUser();
    $guardUser = $user->getGuardUser();

    $contacts  = $guardUser->getConfirmedContacts();

    $invited = array();
    foreach ($contacts as $contact) {
      if (in_array($contact->receiver_id, $contactIds)) {
        $contact->getReceiver()->sentNotification('You have been invited!', $guardUser, $this->conference);
        $invited[] = $contact->getReceiver()->getDisplayName();
      }
    }

    if ($invited) {
      $this->setInfoFlash('Sent invitations to these members: '.join(', ', $invited));
    }
    else {
      $this->setErrorFlash('No invitations made.');
    }

    $this->redirect($this->generateUrl('conference_show', array('id' => $this->conference->getId())));
  }

  public function executeInviteFriends(sfWebRequest $request)
  {
    $this->identifyConferenceById();

    $addresses = $request->getParameter('email_list');
    if (empty($addresses)) {
      $this->setErrorFlash('No email addresses provided.');
      $this->redirect($this->generateUrl('conference_show', array('id' => $this->conference->getId())));
    }

    list($successful, $failed) = $this->validateEmailAddresses($addresses);

    if (count($successful)) {
      // Create temporary user objects in database.

    }

    if (count($successful)) {
      $this->setInfoFlash('[DUMMY] Sent invitations to these addresses: '.implode(', ', $successful));
    }
    if (count($failed)) {
      $this->setErrorFlash('The following email addresses are invalid: '.implode(', ', $failed));
    }

    $this->redirect($this->generateUrl('conference_show', array('id' => $this->conference->getId())));
  }

  protected function validateEmailAddresses($addressString)
  {
    $successful = array();    // Successful sent an invitation
    $failed     = array();    // Validator error

    $validator = new sfValidatorEmail();
    $addresses = explode(' ', $addressString);

    foreach ($addresses as $address) {
      try {
        $clean = $validator->clean($address);
        $successful[] = $clean;
      }
      catch (sfValidatorError $e) {
        $failed[] = $address;
      }
    }

    return array($successful, $failed);
  }

  public function executeShowConference(sfWebRequest $request)
  {
    // Do not use forwards for requests to the API
    $this->identifyConferenceById(!$this->isApiCall);

    if ($this->isApiCall) {
      if (!$this->conference instanceof Conference) {
        $this->handleApiErrorAndStop(404);
      }
      else {
        $this->getResponse()->setStatusCode(200);
      }
    }

    // Preloading
    $this->conference->getCreator();
    $this->conference->getAttendees();
    $this->conference->getCategories();

    if ($this->getUser()->isAuthenticated()) {
      $currentUserId = $this->getUser()->getId();
    }
    else {
      $currentUserId = null;
    }

    // Prepare categories for template.
    $this->hasCategories   = (bool) count($this->conference->Categories);

    // Prepare "is administrator" flag for template.
    $user      = $this->getUser();
    $guardUser = $this->getUser()->getGuardUser();

    // Preload current users personal calendar
    $this->defaultCalendarItems = array();
    if ($user->isAuthenticated()) {
      $this->defaultCalendarItems = $guardUser->getDefaultCalendarItems()->getPrimaryKeys();
    }

    $this->isAdministrator = $user->isAdmin();

    $this->currentUserAttending = in_array(
      $currentUserId,
      $this->conference->getAttendees()->getPrimaryKeys()
    );
    // Current user the owner of the conference?
    $this->currentUserOwner = ($currentUserId == $this->conference->getUserId());

    $this->contacts = array();
    if ($guardUser instanceof sfGuardUser) {
      $this->contacts = $guardUser->getConfirmedContacts();
    }

  }

  public function executeToggleAttendance(sfWebRequest $request)
  {
    $this->identifyConferenceById();

    $currentUserId = $this->getUser()->getGuardUser()->getId();
    $attendeeIds   = $this->conference->getAttendees()->getPrimaryKeys();

    if (in_array($currentUserId, $attendeeIds)) {
      $this->conference->unlink('Attendees', array($currentUserId));
      $this->setInfoFlash('You no longer plan to attend at "'.$this->conference->getName().'"');
    }
    else {
      $this->conference->link('Attendees', array($currentUserId));
      $this->setInfoFlash('You plan to attend at "'.$this->conference->getName().'"');
    }

    $this->conference->save();

    $this->redirect($this->generateUrl('conference_show', array(
      'id' => $this->conference->getId()
    )), 303);
  }

  public function executeShowConferenceIcalendar(sfWebRequest $request)
  {
    $this->identifyConferenceById();

    $withAttendees = (bool) $request->getParameter('members', 0) == 1;

    $calendar      = new vcalendar();
    $selectedEvent = new vevent();

    // All fields are in english by default
    $calendar->setConfig('language', 'en');
    $calendar->setConfig('allowEmpty', false);

    foreach ($this->conference->getCategories() as $aCategory) {
      // Add categories
      $selectedEvent->setProperty('categories', $aCategory->getName());
    }

    // Generate a description
    $descr = '';

    // Basic information (we don't care if something is empty here)
    $descr .= 'Description:'.chr(13).chr(13).$this->conference->getDescription().chr(13).chr(13);
    $descr .= 'Venue:'.chr(13).chr(13).$this->conference->getVenue().chr(13).chr(13);
    $descr .= 'Accomodation:'.chr(13).chr(13).$this->conference->getAccomodation().chr(13).chr(13);
    $descr .= 'How to find:'.chr(13).chr(13).$this->conference->getAccomodation().chr(13).chr(13);

    if ($this->conference->getSeries()->exists()) {
      // Add series information (if any)
      $descr .= 'Part of the series:'.chr(13).chr(13).$this->conference->getSeries()->getName().chr(13).chr(13);
    }

    if ($this->conference->getCreator()->exists()) {
      // Add conference creator
      $descr .= 'Conference creator:'.chr(13).chr(13).$this->conference->getCreator()->getDisplayName();
      if ($this->conference->getCreator()->isContactOf()) {
        $descr .= ' / '.$this->conference->getCreator()->getEmailAddress();
      }
    }

    $selectedEvent->setProperty('description', $descr);
    $selectedEvent->setProperty('SUMMARY', $this->conference->getName());
    $selectedEvent->setProperty('location', 'Hamburg');

    $dateParts = getdate(strtotime($this->conference->getStartdate()));

    $selectedEvent->setProperty('dtstart',
      $dateParts['year'], $dateParts['mon'], $dateParts['mday'], 0, 0, 0);

      $dateParts = getdate(strtotime($this->conference->getEnddate()));

    // Hopefully most of the calendar applications will ready the time 00:00:00
    // in both start and end date as 'full day event' (outlook does)
    $selectedEvent->setProperty('dtend',
      $dateParts['year'], $dateParts['mon'], $dateParts['mday'], 0, 0, 0);

    $calendar->addComponent($selectedEvent);

    $response = $this->getResponse();
    $response->setContentType('text/calendar');

    $filename = substr($this->conference->getName(), 0, 50).'.ics';
    $response->setHttpHeader('content-disposition', 'attachment; filename='.urlencode($filename));

    $this->setLayout(false);

    return $this->renderText($calendar->createCalendar());
  }

  public function executeShowConferencePdf(sfWebRequest $request)
  {
    $this->identifyConferenceById();

    echo 'DOWNLOAD';
    exit;
  }

  public function executeShowConferenceAttendees(sfWebRequest $request)
  {
    $this->identifyConferenceById();

  }

  public function executeShowConferenceAttendee(sfWebRequest $request)
  {
    $this->identifyConferenceById();

    $username = $request->getParameter('username');

    $attendee = $this->getDoctrineTable('sfGuardUser')->findOneByUsername($username);
    $this->forward404Unless($attendee instanceof sfGuardUser);

    $this->attendee   = $attendee;
  }

  // -------------------

  public function executeEditConference(sfWebRequest $request)
  {
    $user      = $this->getUser();
    $guardUser = $user->getGuardUser();

    if ($request->getParameter('start')) {
      $user->invalidatePrgData('conference');
    }

    $incomingData = $user->retrievePrgData('conference');

    $id           = $request->getParameter('id');

    $conference = null;
    if ($id) {
      $conference = $this->getDoctrineTable('Conference')->find($id);
    }
    if (!$conference instanceof Conference) {
      $conference = $this->createModel('Conference');
    }

    if ($conference->exists()) {
      // Only the creator of a conference and the administrator
      // are allowed to modify it.
      if ($conference->getCreator()->getId() != $guardUser->getId() && !$guardUser->hasPermission('admin')) {
        $this->setErrorFlash('Insufficient permissions');
        $this->redirect($this->generateUrl('conference_show', array(
          'id' => $conference->getId(),
        )));
      }
    }

    $editForm = new ConferenceForm($conference, array(
      'user' => $user->getGuardUser(),
    ));

    if ($incomingData && is_array($incomingData)) {
      $editForm->bind($incomingData);
    }

    // ------------------

    $this->editForm   = $editForm;

    $this->conference = $conference;
  }

  protected function processIncomingConferenceObject()
  {
    $data = $this->fetchSubmittedDataObject();
    if (is_null($data)) {
      // Creepy data or just nothing received.
      $this->handleApiErrorAndStop(400);
    }

    $incomingData = array();
    $allowedFields = array('name', 'startdate', 'enddate', 'description',
      'location', 'venue', 'accomodation', 'howtofind');

    foreach ($allowedFields as $aFieldName) {
      // Fetch allowed fields from the submitted object.
      $incomingData[$aFieldName] = isset($data[$aFieldName]) ? $data[$aFieldName] : null;
    }

    // TODO: Split GPS
    // Creator: Cannot be set from outside, will be set to the current apiGuardUser automatically.
    //die(print_r($data, 1));
    if (isset($data['series']['id']) && $data['series']['id']) {

      // Try to find the given series by ID.
      $series = $this->getDoctrineTable('Series')->findOneById($data['series']['id']);
      if (!$series instanceof Series) {
        // Series was given, but not found.
        $this->handleApiErrorAndStop(400);
      }
    }
    elseif (isset($data['series']['name'])) {
      // Try to find the given series by name.
      $series = $this->getDoctrineTable('Series')->findOneById($data['series']['id']);
      if (!$series instanceof Series) {
        // Series was given, but not found.
        $this->handleApiErrorAndStop(400);
      }
    }

    // Not part of a series by default.
    $incomingData['series_id'] = null;

    if ($series instanceof Series) {
      // Check permissions on that series.
      $contacts  = $series->getContacts()->getPrimaryKeys();
      $guardUser = $this->getUser()->getGuardUser();
      if (!$guardUser->hasPermission('admin')&& !in_array($guardUser->getId(), $contacts)) {
        // Not allowed, SORRY
        $this->handleApiErrorAndStop(403);
      }
      else {
        $incomingData['series_id'] = $series->getId();
      }
    }

    // Categories
    if (isset($data['categories'])) {
      if (!is_array($data['categories'])) {
        // Must be an array.
        $this->handleApiErrorAndStop(400);
      }

      $ids = array();

      // Collect given category IDs.
      foreach ($data['categories'] as $aCategory) {
        if (isset($aCategory['id'])) {
          $ids[] = $aCategory['id'];
        }
      }

      // Add category IDs to data array.
      $incomingData['cateogries_list'] = $ids;
    }

    // Let sfForm handle the rest.
    return $incomingData;
  }

  public function executeSaveConference(sfWebRequest $request)
  {
    $user      = $this->getUser();
    $guardUser = $user->getGuardUser();

    if ($this->isApiCall) {
      $incomingData = $this->processIncomingConferenceObject();
    }
    else {
      $incomingData = $request->getParameter('conference', array());
    }

    $id           = $incomingData['id'];

    $conference = null;
    if ($id) {
      $conference = $this->getDoctrineTable('Conference')->find($id);
    }
    if (!$conference instanceof Conference) {
      $id         = 0;
      $conference = $this->createModel('Conference');
    }

    if ($conference->exists()) {
      // Only the creator of a conference and the administrator
      // are allowed to modify it.
      if ($conference->getCreator()->getId() != $guardUser->getId() && !$guardUser->hasPermission('admin')) {
        $this->setErrorFlash('Insufficient permissions');
        $this->redirect($this->generateUrl('conference_show', array(
          'id' => $conference->getId(),
        )));
      }
    }

    $editForm = new ConferenceForm($conference, array(
      'user' => $user->getGuardUser(),
    ), $this->isApiCall ? false : null);

    $editForm->bind($incomingData);

    $valid = $editForm->isValid();

    if (!$valid) {
      if ($this->isApiCall) {
        $this->handleApiErrorAndStop(400);
      }
      else {
        $user->storePrgData('conference', $incomingData);
        $this->redirect('@conference_edit?id='.$id, 303);
      }
    }
    else {

      $isNew = $editForm->getObject()->isNew();

      $editForm->save();

      $id = $conference->getId();

      if ($isNew) {
        // Remember creator
        $conference->setUserId($user->getGuardUser()->getId());
        $conference->save();
      }

      if ($this->isApiCall) {
        // No content in API response.
        $this->getResponse()->setStatusCode(200);
        $this->getResponse()->setContentType('text/plain');
        return $this->renderText('');
      }
      else {
        $user->invalidatePrgData('conference');
        $this->redirect('@conference_show?id='.$id, 303);
      }
    }
  }

  public function executeExpertSearch(sfWebRequest $request)
  {
    $query = $request->getParameter('query');
    $parser = new PlatformsSearchQueryParser($this->getContext());
    $searchParams = $parser->parse($query);
    $searchParams['expert_term'] = $query;
    $this->redirect('@conferences?'.http_build_query(array('f' => $searchParams)));
  }
}
