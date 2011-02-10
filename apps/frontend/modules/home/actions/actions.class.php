<?php

/**
 * home actions.
 *
 * origin: GM
 *
 * @package    platforms
 * @subpackage home
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */
class homeActions extends mwActions
{
  /**
   * @var integer
   */
  const SECONDS_UNTIL_404_META_REFRESH = 20;

  /**
   * Executes index action
   *
   * @param sfWebRequest $request A request object
   */
  public function executeIndex(sfWebRequest $request)
  {
    $this->redirect('categories');
  }

  /**
   * Executes 404 action
   *
   * @param sfWebRequest $request A request object
   */
  public function executeError404(sfWebRequest $request)
  {
    $this->getResponse()->addHttpMeta('refresh', self::SECONDS_UNTIL_404_META_REFRESH.'; URL='.$this->generateUrl('categories'));
  }
}