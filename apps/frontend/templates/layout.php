<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.ico" />
    <?php include_stylesheets() ?>
    <?php include_javascripts() ?>
  </head>
  <body>
    <div>
      <a href="<?php echo url_for('@homepage'); ?>"><img src="/images/plat_forms_logo.png" /></a>
      <a href="http://www.mindworks.de"><img src="/images/mindworks_logo.gif" style="float:right;" /></a>
      <div class="clear"></div>
    </div>
    <ul class="mainmenu">
      <li>
        <a href="<?php echo url_for('@categories'); ?>"><strong>CaP</strong> (Conferences and Participants)</a>
      </li>
      <?php if ($sf_user->isAuthenticated()): ?>
        <li>
          <?php echo link_to('My Profile', '@member_show?username='.$sf_user->getGuardUser()->getUsername()); ?>
        </li>
        <li class="rSeperator">
          <?php echo link_to('Logoff', '@sf_guard_signout'); ?>
          (<?php echo $sf_user->getGuardUser()->getUserName(); ?>)
        </li>
      <?php else: ?>
        <li><?php echo link_to('Login', '@sf_guard_signin'); ?></li>
        <li class="rSeperator"><?php echo link_to('Sign up', '@sf_guard_register'); ?></li>
      <?php endif; ?>
      <li class="lSeperator">
        <?php echo link_to('Conferences', '@conferences'); ?>
        <?php if ($sf_user->getId()): ?>
          [<?php echo link_to('new', '@conference_edit?id=0&start=1'); ?>]
        <?php endif; ?>
      </li>
      <li class="rSeperator">
        <?php echo link_to('Members', '@members'); ?>
      </li>
      <li class="lSeperator">
        <?php echo link_to('Categories', '@categories'); ?>
        <?php if ($sf_user->isAdmin()): ?>
          [<?php echo link_to('new', '@category_edit?id=0&start=1'); ?>]
        <?php endif; ?>
      </li>
      <li class="rSeperator">
        <?php echo link_to('Series', '@series'); ?>
        <?php if ($sf_user->isAdmin()): ?>
          [<?php echo link_to('new', '@series_edit?id=0&start=1'); ?>]
        <?php endif; ?>
      </li>
    </ul>

    <img src="/images/comment-icon.png" alt="" class="icon" />
    <p class="indent">
      Conferences and Participants (CaP) is a simple portal for organizing and searching for conferences
      of different kinds: organizers create conferences in certain categories, add details like venue and
      acommodation and invite participants.<br /><br />
      Interested users can browse categories, search for conferences,
      register for a conference and create personal calendars for conferences they are interested in.
    </p>
    <br />
    <div style="clear:left"></div>

    <?php if ($sf_user->hasFlash('info')): ?>
      <div class="infoFlash"><?php echo $sf_user->getFlash('info'); ?></div>
    <?php endif; ?>

    <?php if ($sf_user->hasFlash('error')): ?>
      <div class="errorFlash"><?php echo $sf_user->getFlash('error'); ?></div>
    <?php endif; ?>

    <?php echo $sf_content ?>
  </body>
</html>
