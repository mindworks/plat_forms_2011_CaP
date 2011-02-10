<?php

/**
 * Conference form.
 *
 * origin: GM
 *
 * @package    platforms
 * @subpackage form
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ConferenceForm extends BaseConferenceForm
{
  public function configure()
  {
    unset(
      $this['user_id'],
      $this['attendees_list'],
      $this['sf_guard_user_list'],
      $this['longitude'],
      $this['latitude'],
      $this['country']
    );

    $this->setWidget('startdate', new sfWidgetFormInputText());
    $this->setWidget('enddate', new sfWidgetFormInputText());

    $this->setValidator('startdate', new sfValidatorDate());
    $this->setValidator('enddate', new sfValidatorDate());

    $this->setWidget('gps_coordinates', new sfWidgetFormInputText());
    $this->setValidator('gps_coordinates', new sfValidatorRegex(
      array(
        'required'    => false,
        'empty_value' => null,
        'trim'        => true,
        'pattern'     => '/\d+(\.\d+)? ?[NnSs] ?,? ?\d+(\.\d+)? ?[EeWw]/',
      ),
      array(
       'invalid'      => 'Invalid format (example: 49.417716N,11.113712E)',
      )
    ));

    $user = $this->getOption('user');

    if (!$user instanceof sfGuardUser) {
      throw new RuntimeException(__CLASS__.' requires the "user" option (of type sfGuardUser).');
    }

    if (!$user->hasPermission('admin')) {
      $query = Doctrine_Core::getTable('Series')->createQuery('a');
      $query->leftJoin('a.Contacts c');
      $query->where('c.id = ?', $user->getId());

      $this->widgetSchema['series_id']->setOption('query', $query);
    }

    $this->markAsMandatory(array('name', 'startdate', 'enddate'));


    $this->mergePostValidator(new sfValidatorSchemaCompare('startdate', '<=', 'enddate', array(), array(
      'invalid' => 'Start date must be earlier than or equal to the enddate.',
    )));
  }

  protected function markAsMandatory(array $fieldNames)
  {
    foreach($fieldNames as $aFieldName) {
      $this->widgetSchema[$aFieldName]->setLabel($this[$aFieldName]->renderLabel().' (*)');
    }
  }

  /**
   * @see sfFormObject
   */
//  protected function doUpdateObject($values)
//  {
//    parent::doUpdateObject($values);
//
//    $this->getObject()->setGpsCoordinates($values['gps_coordinates']);
//  }
}
