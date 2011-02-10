<?php
/**
 * conferences web service index template.
 *
 * origin: M
 *
 * @package    platforms
 * @subpackage conferences
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */
?>
<h2>CONFERENCES INDEX</h2>

<ul>
  <?php foreach ($conferences as $conference): ?>
    <li>
      <?php echo link_to($conference->getName(), '@conference_show?id='.$conference->getId()); ?>
    </li>
  <?php endforeach; ?>
</ul>