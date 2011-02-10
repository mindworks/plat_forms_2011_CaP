<?php
/**
 * series show template.
 *
 * origin: M
 *
 * @package    platforms
 * @subpackage series
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */
?>
<h2>Series: <?php echo $series->getName(); ?></h2>

<?php if ($sf_user->isAuthenticated() && $sf_user->isAdmin()): ?>
  <?php echo link_to('Edit this series', '@series_edit?id='.$series->getId().'&start=1'); ?><br />
  <br />
<?php endif; ?>

<h3>Conferences</h3>
<table>
  <thead>
    <tr>
      <th>Name of the conference</th>
    </tr>
  </thead>
  <?php if (!count($conferences)): ?>
  <tbody>
    <tr><td>None so far.</td></tr>
  </tbody>
  <?php endif; ?>
  <tbody>
    <?php foreach ($conferences as $conference): ?>
      <tr>
        <td>
          <?php echo link_to($conference->getName(), '@conference_show?id='.$conference->getId()); ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

  <h3>Series contacts</h3>
  <table>
    <thead>
      <tr>
        <th>Name</th>
        <th>Town</th>
        <th>Country</th>
        <th>Since</th>
      </tr>
    </thead>
    <?php if (!count($contacts)): ?>
      <tbody>
        <tr><td colspan="4">None so far.</td></tr>
      </tbody>
    <?php else: ?>
      <tbody>
        <?php foreach ($contacts as $contact): ?>
          <tr>
            <td>
              <?php echo link_to($contact->getDisplayName(), '@member_show?username='.$contact->getUsername()); ?>
            </td>
            <td><?php echo $contact->getTown(); ?></td>
            <td><?php echo code_to_country($contact->getCountry()); ?></td>
            <td><?php echo $contact->getCreated_at(); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    <?php endif; ?>
  </table>