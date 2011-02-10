<?php

/**
 * sfGuardUser form base class.
 *
 * @method sfGuardUser getObject() Returns the current form's model object
 *
 * @package    platforms
 * @subpackage form
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BasesfGuardUserForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                          => new sfWidgetFormInputHidden(),
      'first_name'                  => new sfWidgetFormInputText(),
      'last_name'                   => new sfWidgetFormInputText(),
      'email_address'               => new sfWidgetFormInputText(),
      'username'                    => new sfWidgetFormInputText(),
      'algorithm'                   => new sfWidgetFormInputText(),
      'salt'                        => new sfWidgetFormInputText(),
      'password'                    => new sfWidgetFormInputText(),
      'is_active'                   => new sfWidgetFormInputCheckbox(),
      'is_super_admin'              => new sfWidgetFormInputCheckbox(),
      'last_login'                  => new sfWidgetFormDateTime(),
      'fullname'                    => new sfWidgetFormInputText(),
      'town'                        => new sfWidgetFormInputText(),
      'country'                     => new sfWidgetFormInputText(),
      'created_at'                  => new sfWidgetFormDateTime(),
      'updated_at'                  => new sfWidgetFormDateTime(),
      'latitude'                    => new sfWidgetFormInputText(),
      'longitude'                   => new sfWidgetFormInputText(),
      'groups_list'                 => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'sfGuardGroup')),
      'permissions_list'            => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'sfGuardPermission')),
      'default_calendar_items_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Conference')),
      'series_list'                 => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Series')),
    ));

    $this->setValidators(array(
      'id'                          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'first_name'                  => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'last_name'                   => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'email_address'               => new sfValidatorString(array('max_length' => 255)),
      'username'                    => new sfValidatorString(array('max_length' => 128)),
      'algorithm'                   => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'salt'                        => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'password'                    => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'is_active'                   => new sfValidatorBoolean(array('required' => false)),
      'is_super_admin'              => new sfValidatorBoolean(array('required' => false)),
      'last_login'                  => new sfValidatorDateTime(array('required' => false)),
      'fullname'                    => new sfValidatorString(array('max_length' => 255)),
      'town'                        => new sfValidatorString(array('max_length' => 255)),
      'country'                     => new sfValidatorString(array('max_length' => 255)),
      'created_at'                  => new sfValidatorDateTime(),
      'updated_at'                  => new sfValidatorDateTime(),
      'latitude'                    => new sfValidatorPass(array('required' => false)),
      'longitude'                   => new sfValidatorPass(array('required' => false)),
      'groups_list'                 => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'sfGuardGroup', 'required' => false)),
      'permissions_list'            => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'sfGuardPermission', 'required' => false)),
      'default_calendar_items_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Conference', 'required' => false)),
      'series_list'                 => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Series', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
        new sfValidatorDoctrineUnique(array('model' => 'sfGuardUser', 'column' => array('email_address'))),
        new sfValidatorDoctrineUnique(array('model' => 'sfGuardUser', 'column' => array('username'))),
      ))
    );

    $this->widgetSchema->setNameFormat('sf_guard_user[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'sfGuardUser';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['groups_list']))
    {
      $this->setDefault('groups_list', $this->object->Groups->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['permissions_list']))
    {
      $this->setDefault('permissions_list', $this->object->Permissions->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['default_calendar_items_list']))
    {
      $this->setDefault('default_calendar_items_list', $this->object->DefaultCalendarItems->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['series_list']))
    {
      $this->setDefault('series_list', $this->object->Series->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveGroupsList($con);
    $this->savePermissionsList($con);
    $this->saveDefaultCalendarItemsList($con);
    $this->saveSeriesList($con);

    parent::doSave($con);
  }

  public function saveGroupsList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['groups_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Groups->getPrimaryKeys();
    $values = $this->getValue('groups_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Groups', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Groups', array_values($link));
    }
  }

  public function savePermissionsList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['permissions_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Permissions->getPrimaryKeys();
    $values = $this->getValue('permissions_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Permissions', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Permissions', array_values($link));
    }
  }

  public function saveDefaultCalendarItemsList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['default_calendar_items_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->DefaultCalendarItems->getPrimaryKeys();
    $values = $this->getValue('default_calendar_items_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('DefaultCalendarItems', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('DefaultCalendarItems', array_values($link));
    }
  }

  public function saveSeriesList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['series_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Series->getPrimaryKeys();
    $values = $this->getValue('series_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Series', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Series', array_values($link));
    }
  }

}
