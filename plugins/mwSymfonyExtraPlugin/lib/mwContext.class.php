<?php

/**
 * Context used in mindworks projects.
 *
 * origin: RM
 *
 * @package    mwSymfonyExtraPlugin
 * @subpackage lib
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */
class mwContext extends sfContext
{

  /**
   * Get table instance for model name.
   *
   * @param string $modelName
   * @return Doctrine_Table
   */
  public function getDoctrineTable($modelName)
  {
    return Doctrine_Core::getTable($modelName);
  }

  /**
   * Create an empty instance for the given model name.
   *
   * @param string $modelName
   * @return Doctrine_Record
   */
  public function createModel($modelName)
  {
    return new $modelName();
  }

  /**
   * Create a new form instance for the given form name.
   * If a model is also given, it is injected into the form.
   *
   * @param string $formName
   * @param Doctrine_Record $model
   * @param array  An array of options
   * @param string A CSRF secret (false to disable CSRF protection, null to use the global CSRF secret)
   * @return BaseFormDoctrine
   */
  public function createForm($formName, $model = null, $options = array(), $CSRFSecret = null)
  {
    return new $formName($model, $options, $CSRFSecret);
  }
}