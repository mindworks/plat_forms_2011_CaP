<?php

/**
 * Base class for components in mindworks projects.
 *
 * origin: RM
 *
 * @package    mwSymfonyExtraPlugin
 * @subpackage lib
 * @author     Melwin Meyer <mmeyer@mindworks.de>
 * @version    SVN: $Id: $
 */
abstract class mwComponent extends sfComponent
{
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
}