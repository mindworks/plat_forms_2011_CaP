<?php

/**
 * ConferenceCategory form base class.
 *
 * @method ConferenceCategory getObject() Returns the current form's model object
 *
 * @package    platforms
 * @subpackage form
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseConferenceCategoryForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'conference_id' => new sfWidgetFormInputHidden(),
      'category_id'   => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'conference_id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('conference_id')), 'empty_value' => $this->getObject()->get('conference_id'), 'required' => false)),
      'category_id'   => new sfValidatorChoice(array('choices' => array($this->getObject()->get('category_id')), 'empty_value' => $this->getObject()->get('category_id'), 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('conference_category[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ConferenceCategory';
  }

}
