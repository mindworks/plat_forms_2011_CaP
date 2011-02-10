<?php

/**
 * Session user
 *
 * origin: GM
 *
 * @package    platforms
 * @subpackage lib
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */
class myUser extends mwBasicSecurityUser
{
  private $apiGuardUser = null;

  public function setApiGuardUser(sfGuardUser $user)
  {
    $this->apiGuardUser = $user;
  }

  public function getApiGuardUser()
  {
    return $this->apiGuardUser;
  }

  public function getGuardUser()
  {
    if ($this->apiGuardUser instanceof sfGuardUser) {
      // Prefer the apiGuardUser over the session user, if set.
      return $this->getApiGuardUser();
    }
    else {
      // Otherwise take the session user.
      return parent::getGuardUser();
    }
  }

  public function getId()
  {
    $user = $this->getGuardUser();
    if ($this->isAuthenticated()) {
      return $user->getId();
    }
  }

  public function isAdmin()
  {
    $isAdmin = $this->getId() ? $this->getGuardUser()->hasPermission('admin') : false;
    return $isAdmin;
  }

  public function isAuthenticated()
  {
    $request  = sfContext::getInstance()->getRequest();
    $response = sfContext::getInstance()->getResponse();

    if ($request->hasParameter('viaRest') && $request->getParameter('viaRest')) {

      // For all REST routes, HTTP credentials are required.
      if (!isset($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) || empty($_SERVER['PHP_AUTH_PW'])) {
        $this->requestCredentials('Missing credentials');

        return;
      }

      $params = array('username' => $_SERVER['PHP_AUTH_USER'],
                      'password' => $_SERVER['PHP_AUTH_PW']);

      $form = new sfGuardFormSignin(array(), array(), false);

      $form->bind($params);

      if (!$form->isValid()) {
        // Invalid credentials submitted
        $this->requestCredentials('Invalid credentials provided');
      }
      else {
        $values = $form->getValues();
        $this->setApiGuardUser($values['user']);
        $this->setAuthenticated(true);
        return true;
      }
    }
    else {

      // Otherwise, rely on sfGuard authentication mechanism.
      return parent::isAuthenticated() && $this->getGuardUser() instanceof sfGuardUser && $this->getGuardUser()->getId();
    }
  }

  protected function requestCredentials($msg)
  {
    $response = sfContext::getInstance()->getResponse();
    $response->setStatusCode(401);
    $response->setHttpHeader('WWW_Authenticate', 'Basic realm="'.$msg.'"');
    $response->send();
    $this->sendAccessDeniedResponse();
  }

  protected function sendAccessDeniedResponse()
  {
    $response = sfContext::getInstance()->getResponse();
    $response->setStatusCode(401);
    $response->setHttpHeader('Content-Type', 'text/plain');
    $response->setContent('401 Unauthorized');
    $response->send();
    die();
  }
}
