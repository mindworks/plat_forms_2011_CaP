<?php

/**
 * sfGuardUser
 *
 * origin: GM
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class sfGuardUser extends PluginsfGuardUser
{
  public function postSave($obj)
  {

    // manage fulltext index
    if ($this->getId()) {

      $searchIndexTable = Doctrine_Core::getTable('SearchIndex');
      $fulltext         = $searchIndexTable->findOneByModelAndObject_id('Member', $this->getId());
      if (!$fulltext instanceof SearchIndex) {
        $fulltext            = new SearchIndex();
        $fulltext->model     = 'Member';
        $fulltext->object_id = $this->getId();
      }

      $body  = '';
      $body .= $this->getUsername().' ';
      $body .= $this->getTown().' ';
      $body .= PlatformUtils::codeToCountry($this->getCountry()).' ';

      // not fullname nor email!

      $fulltext->body = $body;
      $fulltext->save();

    }


    parent::postSave($obj);
  }


  public function formatForApi()
  {
    $data = new StdClass();

    $user = sfContext::getInstance()->getUser()->getApiGuardUser();

    $data->username = $this->getUsername();

    if ($this->isContactOf($user) || $user->hasPermission('admin')) {
      $data->fullname = $this->getFullname();
      $data->email    = $this->getEmailAddress();
    }

    $data->town     = $this->getTown();
    $data->country  = $this->getCountry();
    $data->status   = $this->getContactStatus();

    return $data;
  }

  public function getName()
  {
    return $this->getFullname();
  }

  public function sentNotification($body, sfGuardUser $from = null, Conference $conference = null)
  {
    if ($this->getId()) {
      $notification = sfContext::getInstance()->createModel('Notification');

      $notification->body      = $body;
      $notification->user_id   = $this->getId();

      if ($from instanceof sfGuardUser && $from->getId()) {
        $notification->sender_id = $from->getId();
      }

      if ($conference instanceof Conference && $conference->getId()) {
        $notification->conference_id = $conference->getId();
      }

      return $notification->save();
    }
  }

  public function isRequestable()
  {
    $user = sfContext::getInstance()->getUser();
    if ($this->getId() && $user->getId()) {
      if ($user->getId() != $this->getId()) {
        $contacts = $user->getGuardUser()->getContacts();
        foreach ($contacts as $contact) {
          if ($contact->receiver_id == $this->getId()) {
            return false;
          }
        }
        return true;
      }
    }
    return false;
  }

  public function getDisplayName()
  {
    $user = sfContext::getInstance()->getUser();
    if ($user->isAdmin() || $this->isContactOf()) {
      if ($this->getFullname()) {
        return $this->getFullname().' ('.$this->getUsername().')';
      }
    }
    return $this->getUsername();
  }

  public function isContactOf()
  {
    $user = sfContext::getInstance()->getUser();

    if ($this->getId() && $user->getId()) {
      if ($this->getId() == $user->getId()) {
        return true; // self
      }
      $contacts = $user->getGuardUser()->getContacts();
      foreach ($contacts as $contact) {
        if ($contact->receiver_id == $this->getId() && $contact->status == 'in_contact') {
          return true;
        }
      }
    }
    return false;
  }

  // Retrieves the contact status between the current user
  // and this object.
  public function getContactStatus()
  {
    $user = sfContext::getInstance()->getUser();

    if (!$user->getGuardUser() instanceof sfGuardUser) {
      return null;
    }

    $query = Doctrine_Core::getTable('Contact')->createQuery('a');

    $query->where('sender_id = ?', $user->getGuardUser()->getId());
    $query->andWhere('receiver_id = ?', $this->getId());

    $rec = $query->fetchOne();

    if (!$rec instanceof Contact) {
      return null;
    }

    return $rec->getStatus();
  }

  public function getConfirmedContacts()
  {
    $result   = array();
    $contacts = $this->getContacts();
    foreach ($contacts as $key => $contact) {
      if ($contact->status == 'in_contact') {
        $result[] = $contact;
      }
    }
    return $result;
  }
  public function getPendingContactRequestsSent()
  {
    $result   = array();
    $contacts = $this->getContacts();
    foreach ($contacts as $key => $contact) {
      if ($contact->status == 'RCD_sent') {
        $result[] = $contact;
      }
    }
    return $result;
  }
  public function getPendingContactRequestsReceived()
  {
    $result   = array();
    $contacts = $this->getContactsOf();
    foreach ($contacts as $key => $contact) {
      if ($contact->status == 'RCD_sent') {
        $result[] = $contact;
      }
    }
    return $result;
  }
}