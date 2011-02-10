<?php
/**
 * members show contacts template.
 *
 * origin: M
 *
 * @package    platforms
 * @subpackage members
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */
?>
<h2>Contacts of User: <?php echo $user->getUsername(); ?></h2>

<?php echo link_to('Show '.$user->getUsername(), '@member_show?username='.$user->getUsername()); ?><br />
<br />

<h3>Contacts:</h3>

<ul>
  <?php foreach ($contacts as $contact): ?>
    <li>
      <?php echo link_to('Show '.$contact->getReceiver()->getUsername(), '@member_show?username='.$contact->getReceiver()->getUsername()); ?>
    </li>
  <?php endforeach; ?>
</ul>
