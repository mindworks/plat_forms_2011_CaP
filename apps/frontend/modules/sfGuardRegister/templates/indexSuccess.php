<?php
/**
 * sfGuardRegister index template.
 *
 * origin: GM
 *
 * @package    platforms
 * @subpackage sfGuardRegister
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */
?>
<?php use_helper('I18N') ?>
<h2><?php echo __('Register', null, 'sf_guard') ?></h2>

<?php echo get_partial('sfGuardRegister/form', array('form' => $form)); ?>