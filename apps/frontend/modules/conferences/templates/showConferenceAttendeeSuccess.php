<?php
/**
 * conferences show attendee template.
 *
 * origin: M
 *
 * @package    platforms
 * @subpackage conferences
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */
?>
<h2>Konferenz Teilnehmer: <?php echo $attendee->getUsername(); ?></h2>

<?php echo link_to('Show', '@conference_show?id='.$conference->getId()); ?><br />
<br />


UNUSED