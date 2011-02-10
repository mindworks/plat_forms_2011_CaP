<?php
/**
 * conferences edit template.
 *
 * origin: M
 *
 * @package    platforms
 * @subpackage conferences
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */
?>
<h2>Create/modify conference</h2>
<p>Mandatory Fields are marked with (*)</p>
<br />
<?php if ($conference->getId()): ?>
  <?php echo link_to($conference->getName(), '@conference_show?id='.$conference->getId()); ?><br />
  <br />
<?php endif; ?>

<form action="<?php echo url_for('@conference_save'); ?>" method="post">
  <table>
    <?php echo $editForm; ?>
  </table>

  <br />
  <input type="submit" value="<?php echo $conference->isNew() ? 'Create' : 'Update'; ?>" />
</form>