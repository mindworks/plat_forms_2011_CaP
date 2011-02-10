<?php

/**
 * members actions.
 *
 * origin: GM
 *
 * @package    platforms
 * @subpackage members
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */
class membersActions extends mwActions
{
  /**
   * Executes index action
   *
   * @param sfRequest $request A request object
   */
  public function executeIndex(sfWebRequest $request)
  {

    $guardUserId     = null;
    $this->myCountry = '';
    $this->myTown    = '';
    $this->myLatLon  = array();
    if ($this->getUser()->isAuthenticated()) {
      $guardUser = $this->getUser()->getGuardUser();
      $this->myCountry = $guardUser->getCountry();
      $this->myTown    = $guardUser->getTown();
      $guardUserId     = $guardUser->getId();
      if ($guardUser->getLatitude() != null && $guardUser->getLongitude() != null) {
        $this->myLatLon = array($guardUser->getLatitude(), $guardUser->getLongitude());
      }
    }

    // ----

    $params         = $request->getParameter('f', array());

    $memberSearcher = new MemberSearcher();

    if (isset($params['my_town'])) {
      $params['my_town'] = $this->myTown;
    }
    if (isset($params['my_country'])) {
      $params['my_country'] = $this->myCountry;
    }

    $doCount        = $this->isApiCall ? false : true;

    $members        = $memberSearcher->search($params, 0, 100, $doCount, $guardUserId);

    $this->users    = $members;

    $this->params   = $memberSearcher->getParams();

    $this->count    = $memberSearcher->getCount();

    if ($this->isApiCall) {
      $this->getResponse()->setStatusCode(200);
    }
  }

  public function executeHandleNotifications(sfWebRequest $request)
  {
    $user    = $this->getUser()->getGuardUser();

    $ids     = $request->getParameter('notifications', array());

    // TODO transaction

    if ($ids && is_array($ids)) {

      $notifications = $user->getNotifications();
      $allowedIds    = array();
      foreach ($notifications as $notification) {
        $allowedIds[$notification->getId()] = $notification;
      }

      foreach ($ids as $id) {

        if (isset($allowedIds[$id])) {
          $allowedIds[$id]->delete();
        }
        else {
          throw new Exception('internal error');
        }
      }

    }
    else {
      $this->setErrorFlash('Nothing selected!');
    }

    $this->redirect('@member_show?username='.$user->getUsername(), 303);
  }

  public function executeHandleContactRequests(sfWebRequest $request)
  {

    $user    = $this->getUser()->getGuardUser();

    $ids     = $request->getParameter('members', array());

    $accept  = $request->getParameter('acceptButton', false);
    $decline = $request->getParameter('declineButton', false);

    if ($ids && is_array($ids)) {

      // check if there is not a relation so far
      $contacts   = $user->getContacts();
      $allowedIds = array();
      foreach ($contacts as $contact) {
        if ($contact->status == 'RCD_received') {
          $allowedContacts[$contact->receiver_id] = $contact;
        }
      }

      foreach ($ids as $id) {

        if ($id != $user->getId() && isset($allowedContacts[$id])) {

          $receiverContact = $this->getDoctrineTable('Contact')->findOneBySender_idAndReceiver_id($id, $user->getId());

          if ($receiverContact instanceof Contact && $receiverContact->status == 'RCD_sent') {
            if ($accept) {

              $allowedContacts[$id]->status = 'in_contact';
              $receiverContact->status      = 'in_contact';

              // TODO transaction?
              $allowedContacts[$id]->save();
              $receiverContact->save();

            }
            elseif ($decline) {
              $allowedContacts[$id]->delete();
              $receiverContact->delete();
            }
          }
          else {
            throw new Exception('internal error');
          }

        }
        else {
          $this->setErrorFlash('Some members were not handled!');
        }
      }

    }
    else {
      $this->setErrorFlash('Nothing selected!');
    }

    $this->redirect('@member_show?username='.$user->getUsername(), 303);
  }

  public function executeRequestContacts(sfWebRequest $request)
  {

    $user = $this->getUser()->getGuardUser();

    $ids  = $request->getParameter('members', array());

    if ($ids && is_array($ids)) {

      // check if there is not a relation so far
      $contacts    = $user->getContacts();
      $existingIds = array();
      foreach ($contacts as $contact) {
        $existingIds[$contact->sender_id]   = $contact->sender_id;
        $existingIds[$contact->receiver_id] = $contact->receiver_id;
      }

      foreach ($ids as $id) {

        if ($id != $user->getId() && !isset($existingIds[$id])) {

          $newContact1 = $this->createModel('Contact');
          $newContact1->sender_id   = $user->getId();
          $newContact1->receiver_id = $id;
          $newContact1->status      = 'RCD_sent';

          $newContact2 = $this->createModel('Contact');
          $newContact2->sender_id   = $id;
          $newContact2->receiver_id = $user->getId();
          $newContact2->status      = 'RCD_received';

          $newContact1->save();
          $newContact2->save();

        }
        else {
          $this->setErrorFlash('Some members were not requested!');
        }
      }

    }
    else {
      $this->setErrorFlash('Nothing selected!');
    }

    // todo redirect back to came from?
    $this->redirect('@member_show?username='.$user->getUsername(), 303);
  }

  public function executeEditMember(sfWebRequest $request)
  {
    $username = $request->getParameter('username');

    $user = $this->getDoctrineTable('sfGuardUser')->findOneByUsername($username);
    $this->forward404Unless($user instanceof sfGuardUser);

    if ($user->getId() != $this->getUser()->getId() && !$this->getUser()->getGuardUser()->hasPermission('admin')) {
      $this->setErrorFlash('You cannot modify other users profiles unless you are an administrator.');
      $this->redirect($this->generateUrl('member_show', array('username' => $username)));
    }

    $editForm       = new sfGuardUserForm($user);

    $persistedData = $this->getUser()->retrievePrgData('sf_guard_user');

    if (count($persistedData)) {
      // Restore previous input and trigger validation.
      $editForm->bind($persistedData);
    }

    $this->user     = $user;
    $this->editForm = $editForm;
  }

  public function executeSaveMember(sfWebRequest $request)
  {
    $username = $request->getParameter('username');

    $user = $this->getDoctrineTable('sfGuardUser')->findOneByUsername($username);
    $this->forward404Unless($user instanceof sfGuardUser);

    if ($user->getId() != $this->getUser()->getId() && !$this->getUser()->getGuardUser()->hasPermission('admin')) {
      $this->setErrorFlash('You cannot modify other users profiles unless you are an administrator.');
      $this->redirect($this->generateUrl('member_show', array('username' => $username)));
    }

    $incomingData = $request->getParameter('sf_guard_user', array());

    $form = new sfGuardUserForm($user);
    $form->bind($incomingData);

    if (!$form->isValid()) {
      // Form errors
      $this->getUser()->storePrgData('sf_guard_user', $incomingData);
      $this->redirect($this->generateUrl('member_edit', array(
        'username' => $user->getUsername(),
      )), 303);
    }

    $this->getUser()->invalidatePrgData('sf_guard_user');

    $form->save();

    $this->setInfoFlash('Profile has been updated.');

    $this->redirect($this->generateUrl('member_show', array(
      'username' => $user->getUsername(),
    )));
  }

  public function executeShowMember(sfWebRequest $request)
  {
    //$this->getUser()->getGuardUser()->sentNotification('test'.time(), $this->getUser()->getGuardUser());

    $username = $request->getParameter('username');

    $user = $this->getDoctrineTable('sfGuardUser')->findOneByUsername($username);
    $this->forward404Unless($user instanceof sfGuardUser);

    $contacts                      = $user->getConfirmedContacts();
    $this->contacts                = $contacts;

    if ($user->getId() == $this->getUser()->getId() && !$this->isApiCall) {

      $notifications                 = $user->getNotifications();
      $this->notifications           = $notifications;

      $pendingContactsReceived       = $user->getPendingContactRequestsReceived();
      $this->pendingRequestsReceived = $pendingContactsReceived;

      $pendingContactsSent           = $user->getPendingContactRequestsSent();
      $this->pendingRequestsSent     = $pendingContactsSent;
    }

    // Preloading
    if (!$this->isApiCall) {
      $user->getDefaultCalendarItems();
    }

    $this->user = $user;

    if ($this->isApiCall) {
      $this->getResponse()->setStatusCode(200);
    }
  }

  public function executeShowMemberContacts(sfWebRequest $request)
  {
    $username = $request->getParameter('username');

    $user = $this->getDoctrineTable('sfGuardUser')->findOneByUsername($username);
    $this->forward404Unless($user instanceof sfGuardUser);

    $contacts = $user->getConfirmedContacts();

    $this->user     = $user;
    $this->contacts = $contacts;

    if ($this->isApiCall) {
      if (!count($this->contacts)) {
        header('HTTP/1.0 204 No Content');
        exit;
      }
      else {
        $this->getResponse()->setStatusCode(200);
      }
    }
  }
}
