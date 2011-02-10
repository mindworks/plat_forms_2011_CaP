<?php

/**
 * BaseConferenceAttendee
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property bigint $conference_id
 * @property bigint $user_id
 * @property Conference $Conference
 * @property sfGuardUser $User
 * 
 * @method bigint             getConferenceId()  Returns the current record's "conference_id" value
 * @method bigint             getUserId()        Returns the current record's "user_id" value
 * @method Conference         getConference()    Returns the current record's "Conference" value
 * @method sfGuardUser        getUser()          Returns the current record's "User" value
 * @method ConferenceAttendee setConferenceId()  Sets the current record's "conference_id" value
 * @method ConferenceAttendee setUserId()        Sets the current record's "user_id" value
 * @method ConferenceAttendee setConference()    Sets the current record's "Conference" value
 * @method ConferenceAttendee setUser()          Sets the current record's "User" value
 * 
 * @package    platforms
 * @subpackage model
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseConferenceAttendee extends mwDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('conference_attendee');
        $this->hasColumn('conference_id', 'bigint', null, array(
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
        $this->hasOne('Conference', array(
             'local' => 'conference_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));

        $this->hasOne('sfGuardUser as User', array(
             'local' => 'user_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));
    }
}