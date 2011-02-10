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
<h2>SUBCATEGORIES</h2>

<div class="rowWrapper">
  <div class="contentColumn">
    <h3>Category: <?php echo $selectedCategory->getName(); ?></h3>
    <p>Click a category to see its subcategories.</p>
    <br />
    <p><?php echo link_to('^Back to '.$selectedCategory->getParentName(), $selectedCategory->getParentRoute()); ?></p>
    <br />
    <ul>
      <?php foreach ($selectedCategory->getSubcategories() as $aCategory): ?>
        <li><strong>
          <?php echo link_to($aCategory->getName(), '@category_show?id='.$aCategory->getId()); ?>
        </strong></li>
      <?php endforeach; ?>
    </ul>
  </div>

  <div class="contentColumn">
    <h3>Conferences for category <?php echo $selectedCategory->getName(); ?></h3>
    <p>Click a conference to see its details.</p><br />

    <?php if (!count($conferences)): ?>
      <ul><li>None so far.</li></ul>
    <?php else: ?>
      <ul>
      <?php foreach ($conferences as $aConference): ?>
        <li>
          <strong>
            <?php echo link_to($aConference->getName(), '@conference_show?id='.$aConference->getId()); ?>
          </strong>
        </li>
      <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>

  <div class="clear"></div>
</div>