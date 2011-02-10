<?php

/**
 * Base class for actions in mindworks projects.
 *
 * origin: RM
 *
 * @package    mwSymfonyExtraPlugin
 * @subpackage lib
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */
abstract class mwActions extends sfActions
{

  public function forward404($message = null)
  {
    if ($this->isApiCall) {
      header('HTTP/1.0 404 Not Found');
      exit;
    }
    parent::forward404($message);
  }

  public function forward404Unless($condition, $message = null)
  {
    if (!$condition && $this->isApiCall) {
      header('HTTP/1.0 404 Not Found');
      exit;
    }
    parent::forward404Unless($condition, $message);
  }

  public function preExecute()
  {
    $this->isApiCall = false;
    if ($this->getRequest()->hasParameter('viaRest') && $this->getRequest()->getParameter('viaRest')) {
      $this->getResponse()->setContentType('application/json');
      // Default
      $this->getResponse()->setStatusCode(501);
      $this->setLayout(false);
      $this->setTemplate($this->getContext()->getActionName().'Ws');
      $this->isApiCall = true;
    }
    return parent::preExecute();
  }

  public function handleApiErrorAndStop($code)
  {
    $this->getResponse()->setStatusCode($code);
    $this->getResponse()->setContentType('text/plain');
    $this->getResponse()->send();

    throw new sfStopException();
  }

  public function fetchSubmittedDataObject()
  {
    $requestParams = $this->getRequest()->getParameterHolder()->getAll();

    // Bruteforce search for the right request parameter
    // that holds the json object.
    foreach ($requestParams as $key => $value) {
      $data = json_decode($value, true);

      if ($data) {
        return $data;
      }
    }

    return null;
  }

  /**
   * Convenience method to fetch table class for given model name.
   *
   * @param string $modelName
   * @return Doctrine_Table
   */
  protected function getDoctrineTable($modelName)
  {
    return $this->getContext()->getDoctrineTable($modelName);
  }

  /**
   * Convenience method to create an empty instance for the given model name.
   *
   * @param string $modelName
   * @return Doctrine_Record
   */
  protected function createModel($modelName)
  {
    return $this->getContext()->createModel($modelName);
  }

  /**
   * Convenience method to create an new form instance for the given form name.
   * If a model is also given, it is injected into the form.
   *
   * @param string $formName
   * @param Doctrine_Record $model
   * @param array  An array of options
   * @param string A CSRF secret (false to disable CSRF protection, null to use the global CSRF secret)
   * @return BaseFormDoctrine
   */
  protected function createForm($formName, $model = null, $options = array(), $CSRFSecret = null)
  {
    return $this->getContext()->createForm($formName, $model, $options, $CSRFSecret);
  }

  /**
   * Sets an info flash message to be displayed on the next get request.
   *
   * @param string $message
   */
  protected function setInfoFlash($message)
  {
    $user = $this->getUser();
    $user->setFlash('info', $message);
  }

  /**
   * Sets an error flash message to be dispayed as a modal dialog on the next get request.
   *
   * @param string $message
   */
  protected function setErrorFlash($message)
  {
    $user = $this->getUser();
    $user->setFlash('error', $message);
  }
}