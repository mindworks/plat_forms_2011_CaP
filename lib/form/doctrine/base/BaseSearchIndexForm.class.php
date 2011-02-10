<?php

/**
 * SearchIndex form base class.
 *
 * @method SearchIndex getObject() Returns the current form's model object
 *
 * @package    platforms
 * @subpackage form
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseSearchIndexForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'        => new sfWidgetFormInputHidden(),
      'model'     => new sfWidgetFormChoice(array('choices' => array('Member' => 'Member', 'Conference' => 'Conference'))),
      'object_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('sfGuardUser'), 'add_empty' => true)),
      'body'      => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'        => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'model'     => new sfValidatorChoice(array('choices' => array(0 => 'Member', 1 => 'Conference'), 'required' => false)),
      'object_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('sfGuardUser'), 'required' => false)),
      'body'      => new sfValidatorString(array('max_length' => 65535, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('search_index[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'SearchIndex';
  }

}
