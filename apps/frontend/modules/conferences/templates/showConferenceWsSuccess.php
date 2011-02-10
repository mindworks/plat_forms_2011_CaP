<?php
/**
 * rest api conferences show template.
 *
 * origin: M
 *
 * @package    platforms
 * @subpackage conferences
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */
?>
<?php echo json_encode($sf_data->getRaw('conference')->formatForApi()); ?>