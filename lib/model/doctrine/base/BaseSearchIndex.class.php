<?php

/**
 * BaseSearchIndex
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property enum $model
 * @property bigint $object_id
 * @property string $body
 * @property Doctrine_Collection $sfGuardUser
 * @property Doctrine_Collection $Conference
 * 
 * @method enum                getModel()       Returns the current record's "model" value
 * @method bigint              getObjectId()    Returns the current record's "object_id" value
 * @method string              getBody()        Returns the current record's "body" value
 * @method Doctrine_Collection getSfGuardUser() Returns the current record's "sfGuardUser" collection
 * @method Doctrine_Collection getConference()  Returns the current record's "Conference" collection
 * @method SearchIndex         setModel()       Sets the current record's "model" value
 * @method SearchIndex         setObjectId()    Sets the current record's "object_id" value
 * @method SearchIndex         setBody()        Sets the current record's "body" value
 * @method SearchIndex         setSfGuardUser() Sets the current record's "sfGuardUser" collection
 * @method SearchIndex         setConference()  Sets the current record's "Conference" collection
 * 
 * @package    platforms
 * @subpackage model
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseSearchIndex extends mwDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('search_index');
        $this->hasColumn('model', 'enum', null, array(
             'type' => 'enum',
             'values' => 
             array(
              0 => 'Member',
              1 => 'Conference',
             ),
             'default' => 'conference',
             ));
        $this->hasColumn('object_id', 'bigint', null, array(
             'type' => 'bigint',
             ));
        $this->hasColumn('body', 'string', 65535, array(
             'type' => 'string',
             'notnull' => true,
             'default' => '',
             'length' => 65535,
             ));


        $this->index('speed', array(
             'fields' => 
             array(
              0 => 'object_id',
             ),
             ));
        $this->index('content', array(
             'fields' => 
             array(
              0 => 'body',
             ),
             'type' => 'fulltext',
             ));
        $this->option('type', 'MyISAM');
        $this->option('collate', 'utf8_unicode_ci');
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('sfGuardUser', array(
             'local' => 'object_id',
             'foreign' => 'id'));

        $this->hasMany('Conference', array(
             'local' => 'object_id',
             'foreign' => 'id'));
    }
}