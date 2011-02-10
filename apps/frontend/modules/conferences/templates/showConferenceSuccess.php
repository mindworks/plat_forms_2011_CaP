<?php
/**
 * conferences show template.
 *
 * origin: M
 *
 * @package    platforms
 * @subpackage conferences
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */
?>
<h2>Details of conference: <span class="green"><?php echo $conference->getName(); ?></span></h2>

<h3>Your Status</h3>

<?php if (!$sf_user->isAuthenticated()): ?>
  <img class="icon" src="/images/Calendar-icon.png" alt="" />You need to <a href="<?php echo url_for('@sf_guard_signin'); ?>">login</a> or <a href="<?php echo url_for('@sf_guard_register'); ?>">register</a> if you want to attend to this conference.
<?php elseif ($currentUserAttending): ?>
  <img class="icon" src="/images/Calendar-icon.png" alt="" />You are <strong>attending</strong> to this conference! <a href="<?php echo url_for('@conference_toggle_attendance?id='.(integer)$conference->getId()); ?>">I want to unattend</a>
<?php else: ?>
  <img class="icon" src="/images/Calendar-icon.png" alt="" />You are <strong>not attending</strong> to this conference yet. <a href="<?php echo url_for('@conference_toggle_attendance?id='.(integer)$conference->getId()); ?>">I want to attend!</a>
<?php endif; ?>

<?php if ($currentUserOwner || $isAdministrator): ?>
  <br /><br />
  <img class="icon" src="/images/edit-user-icon.png" alt="" />You are the owner of this conference or an administrator, <?php echo link_to('you may edit it', '@conference_edit?id='.$conference->getId().'&start=1'); ?>.
<?php endif; ?>

<?php if ($sf_user->isAuthenticated() && !in_array($conference->getId(), $sf_data->getRaw('defaultCalendarItems'))): ?>
  <br /><br />
  <img class="icon" src="/images/Favorite-icon.png" alt="" />Would you like to track this conference? <a href="<?php echo url_for('@conference_add_to_calendar?id=' .(integer)$conference->getId()); ?>">Add it to your personal calendar!</a>
<?php elseif ($sf_user->isAuthenticated()): ?>
  <br /><br />
  <img class="icon" src="/images/Favorite-icon.png" alt="" />This conference is on your <a href="<?php echo url_for('@member_show?username='.$sf_user->getUsername().'#calendar'); ?>">personal calendar</a> already. No longer interested? <a href="<?php echo url_for('@conference_remove_from_calendar?id=' .(integer)$conference->getId()); ?>">Remove it.</a>
<?php endif; ?>
<br />
<br />

<h3>Downloads</h3>
<img class="icon" alt="" src="/images/Oficina-PDF-icon.png" /><?php echo link_to('Conference infos as PDF (work in progress)', '@conference_pdf?id='.$conference->getId()); ?><br />
<div style="clear: left"></div>
<img class="icon" alt="" src="/images/iCal-empty-icon.png" /><?php echo link_to('Plain iCalendar', '@conference_icalendar?id='.$conference->getId().'&members=0'); ?><br />
<div style="clear: left"></div>
<img class="icon" alt="" src="/images/iCal-empty-icon.png" /><?php echo link_to('iCalendar with attendees (work in progress)', '@conference_icalendar?id='.$conference->getId().'&members=1'); ?><br />
<br />
<br />

<h3>Conference information</h3>

<table class="plain">
  <tr>
    <th>Title:</th>
    <td><strong><?php echo $conference->getName(); ?></strong></td>
  </tr>
  <tr>
    <th>Takes place from:</th>
    <td>
      <?php echo date('Y-m-d', strtotime($conference->getStartdate())); ?>
      &nbsp;&nbsp;to&nbsp;&nbsp;
      <?php echo date('Y-m-d', strtotime($conference->getEnddate())); ?>
    </td>
  </tr>
  <tr>
    <th>Description:</th>
    <td><?php echo $conference->getDescription(); ?></td>
  </tr>
  <tr>
    <th>Venue:</th>
    <td><?php echo $conference->getVenue(); ?></td>
  </tr>
  <tr>
    <th>Location:</th>
    <td><?php echo $conference->getLocation(); ?></td>
  </tr>
  <tr>
    <th>How to find:</th>
    <td><?php echo $conference->getHowToFind(); ?></td>
  </tr>
  <tr>
    <th>Accomodation:</th>
    <td><?php echo $conference->getAccomodation(); ?></td>
  </tr>
  <?php if ($conference->getSeries()->exists()): ?>
  <tr>
    <th>Part of the series:</th>
    <td><a href="<?php echo url_for('@series_show?id=' . (integer)$conference->getSeries()->getId()); ?>"><?php echo $conference->getSeries()->getName(); ?></a></td>
  </tr>
  <?php endif; ?>
  <?php if ($hasCategories): ?>
  <tr>
    <th>Belongs to these categories:</th>
    <td>
      <?php foreach ($conference->getCategories() as $cat): ?>
        <a href="<?php echo url_for('@category_show?id=' . (integer)$cat->getId()); ?>"><?php echo $cat->getName(); ?></a>
      <?php endforeach; ?>
    </td>
  </tr>
  <?php endif; ?>
</table>

<br />

<h3>List of attendees</h3>
<?php if (0 == count($conference->getAttendees())): ?>
  None so far.
<?php else: ?>
  <table>
    <tr>
      <th>Username</th>
      <th>Fullname</th>
      <th>Email Address</th>
    </tr>
    <?php foreach ($conference->getAttendees() as $aUser): ?>
      <?php if ($sf_user->getId() && ($sf_user->isAdmin() || $sf_user->getId() == $conference->user_id || $aUser->isContactOf())): ?>
        <tr>
          <td><?php echo link_to($aUser->getUsername(), '@member_show?username='.$aUser->getUsername()); ?></td>
          <td><?php echo $aUser->getFullname(); ?></td>
          <td><?php echo $aUser->getEmailAddress(); ?></td>
        </tr>
      <?php else: ?>
        <tr>
          <td><?php echo link_to($aUser->getUsername(), '@member_show?username='.$aUser->getUsername()); ?></td>
          <td>[hidden]</td>
          <td>[hidden]</td>
        </tr>
      <?php endif; ?>
    <?php endforeach; ?>
  </table>
<?php endif; ?>
<br /><br />

<h3>Invite your friends and colleagues</h3>
<p>Enter a space-separated list of email addresses of your friends to invite.</p>
<br />
<?php echo form_tag(url_for('@conference_invite_friends?id='.(integer)$conference->getId())); ?>
  <input type="text" class="largeTextbox" name="email_list" />
  <input type="submit" value="Invite!" />
<?php echo '</form>'; ?>

<?php if (count($contacts)): ?>
  <strong>OR</strong> Invite contacts<br />
  <?php echo form_tag(url_for('@conference_invite_contacts?id='.(integer)$conference->getId())); ?>
    <select name="contacts[]" multiple="multiple">
      <?php foreach ($contacts as $contact): ?>
        <option value="<?php echo $contact->receiver_id; ?>"><?php echo $contact->getReceiver()->getDisplayName(); ?></option>
      <?php endforeach; ?>
    </select>
    <br />
    <br />
    <input type="submit" value="Invite selected contact(s) to this conference" />
  <?php echo '</form>'; ?>
  <br />
<?php endif; ?>

<h3>Conference created by:</h3>

<?php if ($conference->getUserId()): ?>
  <?php echo link_to($conference->getCreator()->getDisplayName(), '@member_show?username='.$conference->getCreator()->getUserName()); ?>
  <?php if ($sf_user->getId() && ($sf_user->isAdmin() || $conference->getCreator()->isContactOf())): ?>
    / <?php echo $conference->getCreator()->getEmailAddress(); ?>
  <?php endif; ?>
<?php else: ?>
  Unknown (rebuild your database)
<?php endif; ?>

