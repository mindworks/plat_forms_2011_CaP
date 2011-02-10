<?php

/**
 * Contact form base class.
 *
 * @method Contact getObject() Returns the current form's model object
 *
 * @package    platforms
 * @subpackage form
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseContactForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'sender_id'   => new sfWidgetFormInputHidden(),
      'receiver_id' => new sfWidgetFormInputHidden(),
      'status'      => new sfWidgetFormChoice(array('choices' => array('in_contact' => 'in_contact', 'RCD_sent' => 'RCD_sent', 'RCD_received' => 'RCD_received'))),
      'created_at'  => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'sender_id'   => new sfValidatorChoice(array('choices' => array($this->getObject()->get('sender_id')), 'empty_value' => $this->getObject()->get('sender_id'), 'required' => false)),
      'receiver_id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('receiver_id')), 'empty_value' => $this->getObject()->get('receiver_id'), 'required' => false)),
      'status'      => new sfValidatorChoice(array('choices' => array(0 => 'in_contact', 1 => 'RCD_sent', 2 => 'RCD_received'), 'required' => false)),
      'created_at'  => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('contact[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Contact';
  }

}
