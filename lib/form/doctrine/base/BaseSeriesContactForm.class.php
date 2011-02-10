<?php

/**
 * SeriesContact form base class.
 *
 * @method SeriesContact getObject() Returns the current form's model object
 *
 * @package    platforms
 * @subpackage form
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseSeriesContactForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'series_id' => new sfWidgetFormInputHidden(),
      'user_id'   => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'series_id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('series_id')), 'empty_value' => $this->getObject()->get('series_id'), 'required' => false)),
      'user_id'   => new sfValidatorChoice(array('choices' => array($this->getObject()->get('user_id')), 'empty_value' => $this->getObject()->get('user_id'), 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('series_contact[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'SeriesContact';
  }

}
