<?php
/**
 * conferences show attendees template.
 *
 * origin: M
 *
 * @package    platforms
 * @subpackage conferences
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */
?>
<h2>Konferenz Teilnehmer: <?php echo $conference->getName(); ?></h2>

<?php echo link_to('Show', '@conference_show?id='.$conference->getId()); ?><br />
<br />

<?php echo link_to('Teilnehmer: admin', '@conference_attendee?id='.$conference->getId().'&username=admin'); ?><br />
<br />


UNUSED