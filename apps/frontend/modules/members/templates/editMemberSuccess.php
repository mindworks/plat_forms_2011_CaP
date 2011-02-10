<?php
/**
 * members edit template.
 *
 * origin: M
 *
 * @package    platforms
 * @subpackage members
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */
?>
<h2>Edit User: <?php echo $user->getUsername(); ?></h2>

<?php echo link_to('Show '.$user->getUsername(), '@member_show?username='.$user->getUsername()); ?><br />

<br />

<form action="<?php echo url_for('@member_save?username='.$user->getUsername()); ?>">

<?php echo $editForm; ?><br /><br />

<input type="submit" value="Save changes" />

</form>
