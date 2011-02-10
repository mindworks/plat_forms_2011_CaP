<?php
/**
 * series edit template.
 *
 * origin: M
 *
 * @package    platforms
 * @subpackage series
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */
?>
<h2>Create/modify series</h2>

<?php if ($series->getId()): ?>
  <?php echo link_to($series->getName(), '@series_show?id='.$series->getId()); ?><br />
  <br />
<?php endif; ?>

<form action="<?php echo url_for('@series_save'); ?>" method="post">
  <table>
    <?php echo $editForm; ?>
  </table>
  <input type="submit" />
</form>