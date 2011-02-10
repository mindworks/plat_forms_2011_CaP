<?php
/**
 * members show template.
 *
 * origin: M
 *
 * @package    platforms
 * @subpackage members
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */
?>
<div class="memberProfile">
  <h2>Member: <?php echo $user->getUsername(); ?></h2>

  <?php if ($user->getId() == $sf_user->getId()): ?>
    <?php echo link_to('Edit my profile', '@member_edit?username='.$user->getUsername()); ?><br />
    <br />
  <?php elseif ($sf_user->isAuthenticated() && $sf_user->getGuardUser()->hasPermission('admin')): ?>
    <?php echo link_to('Edit '.$user->getUsername(), '@member_edit?username='.$user->getUsername()); ?><br />
    <br />
  <?php endif?>

  <table>
    <tbody>
      <tr>
        <th>Username</th>
        <td><?php echo $user->getUsername(); ?></td>
      </tr>
      <?php if ($sf_user->getId() && ($sf_user->isAdmin() || $user->isContactOf())): ?>
        <tr>
          <th>Full name</th>
          <td><?php echo $user->getFullname(); ?></td>
        </tr>
        <tr>
          <th>E-Mail</th>
          <td><?php echo $user->getEmail_address(); ?></td>
        </tr>
      <?php else: ?>
        <tr>
          <th>Full name</th>
          <td>[hidden]</td>
        </tr>
        <tr>
          <th>E-Mail</th>
          <td>[hidden]</td>
        </tr>
      <?php endif; ?>
      <tr>
        <th>Town</th>
        <td><?php echo $user->getTown(); ?></td>
      </tr>
      <tr>
        <th>Country</th>
        <td><?php echo code_to_country($user->getCountry()); ?></td>
      </tr>
      <?php if ($user->getId() == $sf_user->getId()): ?>
        <tr>
          <th>GPS</th>
          <td><?php echo $user->getLatitude(); ?> <?php echo $user->getLongitude(); ?></td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>

  <?php if ($user->isRequestable()): ?>
    <form action="<?php echo url_for('@member_contact_requests'); ?>" method="post">
      <input type="hidden" name="members[]" value="<?php echo $user->getId(); ?>" />
      <input type="submit" value="Send request for contact details" />
    </form>
    <br />
  <?php endif; ?>

  <?php if ($user->getId() == $sf_user->getId()): ?>
    <h3>Notifications</h3>
    <form action="<?php echo url_for('@notifications_seen'); ?>" method="post">
      <table>
        <thead>
          <tr>
            <th>Select</th>
            <th>Message</th>
            <th>Conference</th>
            <th>From</th>
            <th>Date</th>
          </tr>
        </thead>
        <?php if (!count($notifications)): ?>
          <tbody>
            <tr><td colspan="5">None so far.</td></tr>
          </tbody>
        <?php else: ?>
          <tbody>
            <?php foreach ($notifications as $notification): ?>
              <tr>
                <td>
                  <input name="notifications[]" value="<?php echo $notification->getId(); ?>" type="checkbox" />
                </td>
                <td>
                  <?php echo nl2br($notification->getBody()); ?>
                </td>
                <td>
                  <?php if ($notification->conference_id): ?>
                    <?php echo link_to($notification->getConference()->getName(), '@conference_show?id='.$notification->getConference()->getId()); ?>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($notification->sender_id): ?>
                    <?php echo link_to($notification->getSender()->getDisplayName(), '@member_show?username='.$notification->getSender()->getUsername()); ?>
                  <?php endif; ?>
                </td>
                <td><?php echo $notification->getCreated_at(); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        <?php endif; ?>
      </table>
      <input type="submit" name="acceptButton" value="Remove selected notification(s)" />
    </form>
  <?php endif; ?>

  <h3>Contacts</h3>
  <table>
    <thead>
      <tr>
        <th>Name</th>
        <th>Town</th>
        <th>Country</th>
        <th>Since</th>
      </tr>
    </thead>
    <?php if (!count($contacts)): ?>
      <tbody>
        <tr><td colspan="4">None so far.</td></tr>
      </tbody>
    <?php else: ?>
      <tbody>
        <?php foreach ($contacts as $contact): ?>
          <tr>
            <td>
              <?php echo link_to($contact->getReceiver()->getDisplayName(), '@member_show?username='.$contact->getReceiver()->getUsername()); ?>
            </td>
            <td><?php echo $contact->getReceiver()->getTown(); ?></td>
            <td><?php echo code_to_country($contact->getReceiver()->getCountry()); ?></td>
            <td><?php echo $contact->getReceiver()->getCreated_at(); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    <?php endif; ?>
  </table>

  <?php if ($user->getId() == $sf_user->getId()): ?>
    <h3>Pending Requests Received</h3>
    <form action="<?php echo url_for('@member_handle_requests'); ?>" method="post">
      <table>
        <thead>
          <tr>
            <th>Select</th>
            <th>Name</th>
            <th>Town</th>
            <th>Country</th>
            <th>Requested</th>
          </tr>
        </thead>
        <?php if (!count($pendingRequestsReceived)): ?>
        <tbody>
          <tr><td colspan="5">None so far.</td></tr>
        </tbody>
        <?php else: ?>
          <tbody>
            <?php foreach ($pendingRequestsReceived as $contact): ?>
              <tr>
                <td>
                  <input name="members[]" value="<?php echo $contact->getSender()->getId(); ?>" type="checkbox" />
                </td>
                <td>
                  <?php echo link_to($contact->getSender()->getDisplayName(), '@member_show?username='.$contact->getSender()->getUsername()); ?>
                </td>
                <td><?php echo $contact->getReceiver()->getTown(); ?></td>
                <td><?php echo code_to_country($contact->getReceiver()->getCountry()); ?></td>
                <td>
                  <?php echo $contact->getCreated_at(); ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        <?php endif; ?>
      </table>
      <input type="submit" name="acceptButton" value="Accept selected request(s)" />
      <input type="submit" name="declineButton" value="Reject selected request(s)" />
    </form>

    <h3>Pending Requests Sent</h3>
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Town</th>
          <th>Country</th>
          <th>Requested</th>
        </tr>
      </thead>
      <?php if (!count($pendingRequestsSent)): ?>
      <tbody>
        <tr><td colspan="4">None so far.</td></tr>
      </tbody>
      <?php else: ?>
        <tbody>
          <?php foreach ($pendingRequestsSent as $contact): ?>
            <tr>
              <td>
                <?php echo link_to($contact->getReceiver()->getDisplayName(), '@member_show?username='.$contact->getReceiver()->getUsername()); ?>
              </td>
              <td><?php echo $contact->getReceiver()->getTown(); ?></td>
              <td><?php echo code_to_country($contact->getReceiver()->getCountry()); ?></td>
              <td>
                <?php echo $contact->getCreated_at(); ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      <?php endif; ?>
    </table>

    <a name="calendar"></a>
    <h3>Personal calendar</h3>
    <table>
      <thead>
        <tr>
          <th>Starts at</th>
          <th>Ends at</th>
          <th>Name of the conference</th>
          <th>Remove</th>
        </tr>
      </thead>
      <?php if (!count($user->getDefaultCalendarItems())): ?>
      <tbody>
        <tr>
          <td colspan="4">None so far. <a href="<?php echo url_for('@conferences'); ?>">Click here</a> to find conferences.</td>
        </tr>
      </tbody>
      <?php else: ?>
        <tbody>
        <?php foreach ($user->getDefaultCalendarItems() as $anItem): ?>
          <tr>
            <td><?php echo date('Y-m-d', strtotime($anItem->getStartdate())); ?></td>
            <td><?php echo date('Y-m-d', strtotime($anItem->getEnddate())); ?></td>
            <td><a href="<?php echo url_for('@conference_show?id='.$anItem->getId()); ?>"><?php echo $anItem->getName(); ?></a></td>
            <td><a href="<?php echo url_for('@conference_remove_from_calendar?id=' . (integer)$anItem->getId()); ?>">Remove from calendar</a></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      <?php endif; ?>
    </table>
  <?php endif; ?>
</div>