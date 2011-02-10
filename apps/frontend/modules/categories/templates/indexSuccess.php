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
<h2>MAIN PAGE</h2>

<div class="rowWrapper">
  <h3>What you might want to do:</h3>
  <ul>
    <li><strong><?php echo link_to('Create a new conference', '@conference_edit?id=0&start=1'); ?></strong></li>
    <li><strong><?php echo link_to('Browse for interesting conferences', '@conferences'); ?></strong></li>
    <li><strong><?php echo link_to('Browse for other members', '@members'); ?></strong></li>
  </ul>
</div>
<div class="rowWrapper">
  <div class="contentColumn">
    <h3>Browse by category</h3>
    <p>Click a category to see its subcategories.</p>
    <br />
    <ul>
      <?php foreach ($categories as $category): ?>
        <li><strong>
          <?php echo link_to($category->getName(), '@category_show?id='.$category->getId()); ?>
        </strong></li>
      <?php endforeach; ?>
    </ul>
  </div>

  <div class="contentColumn">
    <h3>Current conferences (total: <?php echo $count; ?>)</h3>
    <p>Click a conference to see its details.</p><br />

    <?php if (!count($conferences)): ?>
      <ul><li>None so far.</li></ul>
    <?php else: ?>
      <ul>
      <?php foreach ($conferences as $conference): ?>
        <li>
          <strong>
            <?php echo link_to($conference->getName(), '@conference_show?id='.$conference->getId()); ?>
          </strong>
        </li>
      <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>

  <div class="clear"></div>
</div>

<div class="rowWrapper">
  <div style="width: 50%; float: left">
    <h3>Conferences running right now</h3>

    <?php if (!count($conferencesToday)): ?>
      <ul><li>None so far.</li></ul>
    <?php else: ?>
      <ul>
      <?php foreach ($conferencesToday as $conference): ?>
        <li>
          <strong>
            <?php echo link_to($conference->getName(), '@conference_show?id='.$conference->getId()); ?>
          </strong>
        </li>
      <?php endforeach; ?>
      </ul>
    <?php endif; ?>

  </div>

  <div style="width: 50%; float: left">
    <h3>Conferences starting tomorrow</h3>

    <?php if (!count($conferencesTomorrow)): ?>
      <ul><li>None so far.</li></ul>
    <?php else: ?>
      <ul>
      <?php foreach ($conferencesTomorrow as $conference): ?>
        <li>
          <strong>
            <?php echo link_to($conference->getName(), '@conference_show?id='.$conference->getId()); ?>
          </strong>
        </li>
      <?php endforeach; ?>
      </ul>
    <?php endif; ?>

  </div>

  <div style="clear: left"></div>
</div>