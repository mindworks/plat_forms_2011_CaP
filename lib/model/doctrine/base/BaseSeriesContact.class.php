<?php

/**
 * BaseSeriesContact
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property bigint $series_id
 * @property bigint $user_id
 * @property Series $Series
 * @property sfGuardUser $User
 * 
 * @method bigint        getSeriesId()  Returns the current record's "series_id" value
 * @method bigint        getUserId()    Returns the current record's "user_id" value
 * @method Series        getSeries()    Returns the current record's "Series" value
 * @method sfGuardUser   getUser()      Returns the current record's "User" value
 * @method SeriesContact setSeriesId()  Sets the current record's "series_id" value
 * @method SeriesContact setUserId()    Sets the current record's "user_id" value
 * @method SeriesContact setSeries()    Sets the current record's "Series" value
 * @method SeriesContact setUser()      Sets the current record's "User" value
 * 
 * @package    platforms
 * @subpackage model
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseSeriesContact extends mwDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('series_contact');
        $this->hasColumn('series_id', 'bigint', null, array(
             'type' => 'bigint',
             'primary' => true,
             ));
        $this->hasColumn('user_id', 'bigint', null, array(
             'type' => 'bigint',
             'primary' => true,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Series', array(
             'local' => 'series_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));

        $this->hasOne('sfGuardUser as User', array(
             'local' => 'user_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));
    }
}