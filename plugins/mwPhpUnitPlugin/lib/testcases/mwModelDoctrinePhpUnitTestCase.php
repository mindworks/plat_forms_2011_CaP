<?php

/**
 * Abstract base test case for Doctrine models.
 *
 * origin: RM
 *
 * @package    mwPhpUnitPlugin
 * @subpackage testcases
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: mwModelDoctrinePhpUnitTestCase.php 688 2010-11-05 13:34:24Z mi_basedow $
 */
abstract class mwModelDoctrinePhpUnitTestCase extends mwDoctrinePhpUnitTestCase
{
  /**
   * Create a model with all mandatory data
   *
   * @return Doctrine_Record $model
   */
  abstract public function createModelInSavableState();

  /**
   * Sunshine saving test case.
   */
  public function testSave()
  {
    $model = $this->createModelInSavableState();

    $model->save();

    // assert that the models primary key is set
    $primaryKey = $model->identifier();
    $this->assertTrue((boolean)reset($primaryKey));

    $modelFromDb = $this->retrieveModelFromDB($model);

    $this->assertTrue($modelFromDb instanceof Doctrine_Record, 'model should be an instance of Doctrine_Record');
  }

  /**
   * @param Doctrine_Record $model
   * @return Doctrine_Record
   */
  protected function retrieveModelFromDB(Doctrine_Record $model)
  {
    $tableName = get_class($model);
    return Doctrine::getTable($tableName)->find($model->identifier());
  }
}