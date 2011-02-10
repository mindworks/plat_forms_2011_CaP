<?php

require_once(dirname(__FILE__).'/../../../../../plugins/sfDoctrineGuardPlugin/modules/sfGuardAuth/lib/BasesfGuardAuthActions.class.php');

/**
 * sfGuardAuth actions.
 *
 * origin: GM
 *
 * @package    platforms
 * @subpackage sfGuardAuth
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */
class sfGuardAuthActions extends BasesfGuardAuthActions
{
  public function executeSignin($request)
  {
    $ret = parent::executeSignin($request);

    if ($request->isMethod('post') && !$request->hasParameter('signin')) {
      $this->getUser()->setReferer($this->generateUrl('homepage'));
      $this->redirect($this->generateUrl('sf_guard_signin'));
    }

    if ($this->getContext()->getActionStack()->getSize() == 1) {
      $referer = $request->getReferer();
      // Rewrite the original referer to the homepage, if we came from a site that requires http authentication.
      if (preg_match('@/ws/|/ws$@', $referer)) {
        $this->getUser()->setReferer($this->generateUrl('homepage'));
      }
    }

    return $ret;
  }
  public function executeSignout($request)
  {
    return parent::executeSignout($request);
  }
  public function executeSecure($request)
  {
    return parent::executeSecure($request);
  }
  public function executePassword($request)
  {
    return parent::executePassword($request);
  }
}