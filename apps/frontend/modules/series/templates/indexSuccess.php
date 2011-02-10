<?php
/**
 * series index template.
 *
 * origin: M
 *
 * @package    platforms
 * @subpackage series
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */
?>
<h2>SERIES INDEX</h2>

<ul>
  <?php foreach ($series as $aSeries): ?>
    <li>
      <?php echo link_to($aSeries->getName(), '@series_show?id='.$aSeries->getId()); ?>
    </li>
  <?php endforeach; ?>
</ul>