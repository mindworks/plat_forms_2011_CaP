<?php
/**
 * conferences edit template.
 *
 * origin: M
 *
 * @package    platforms
 * @subpackage categories
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */
?>
<h2>Create/modify category</h2>

<?php if ($category->getId()): ?>
  <?php echo link_to($category->getName(), '@category_show?id='.$category->getId()); ?><br />
  <br />
<?php endif; ?>

<form action="<?php echo url_for('@category_save'); ?>" method="post">
  <table>
    <?php echo $editForm; ?>
  </table>
  <input type="submit" />
</form>