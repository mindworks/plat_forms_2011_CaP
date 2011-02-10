<?php
/**
 * sfGuardRegister form partial template.
 *
 * origin: M
 *
 * @package    platforms
 * @subpackage sfGuardRegister
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */
?>
<form action="<?php echo url_for('@sf_guard_register') ?>" method="post">
  <h3>Mandatory fields:</h3>

  <?php echo $form->renderGlobalErrors(); ?>
  <?php echo $form->renderHiddenFields(); ?>

  <table>
    <tr>
      <td class="label"><?php echo $form['username']->renderLabel(); ?>:</td>
      <td><?php echo $form['username']->render(); ?> <?php echo $form['username']->renderError(); ?></td>
    </tr>
    <tr>
      <td class="label"><?php echo $form['email_address']->renderLabel(); ?>:</td>
      <td><?php echo $form['email_address']->render(); ?><?php echo $form['email_address']->renderError(); ?></td>
    </tr>
    <tr>
      <td class="label"><?php echo $form['password']->renderLabel(); ?>:</td>
      <td><?php echo $form['password']->render(); ?><?php echo $form['password']->renderError(); ?></td>
    </tr>
    <tr>
      <td class="label"><?php echo $form['password_again']->renderLabel(); ?>:</td>
      <td><?php echo $form['password_again']->render(); ?><?php echo $form['password_again']->renderError(); ?></td>
    </tr>
    <tr>
      <td class="label"><?php echo $form['fullname']->renderLabel(); ?>:</td>
      <td><?php echo $form['fullname']->render(); ?> <?php echo $form['fullname']->renderError(); ?></td>
    </tr>
    <tr>
      <td class="label"><?php echo $form['town']->renderLabel(); ?>:</td>
      <td><?php echo $form['town']->render(); ?><?php echo $form['town']->renderError(); ?></td>
    </tr>
    <tr>
      <td class="label"><?php echo $form['country']->renderLabel(); ?>:</td>
      <td><?php echo $form['country']->render(); ?><?php echo $form['country']->renderError(); ?></td>
    </tr>
  </table>

  <h3>Optional:</h3>

  <input type="submit" name="register" value="<?php echo __('Register', null, 'sf_guard') ?>" />

</form>