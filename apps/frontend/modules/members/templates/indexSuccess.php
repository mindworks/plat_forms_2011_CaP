<?php
/**
 * members index template.
 *
 * origin: M
 *
 * @package    platforms
 * @subpackage members
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */
?>

<?php $myLatLon = $sf_data->getRaw('myLatLon'); ?>

<h2>Search for members</h2>

<form action="?" class="searchPanel">

  <fieldset>
    <label for="term">Query:</label>
    <input type="text" name="f[term]" id="term" value="<?php echo isset($params['term']) ? $params['term'] : ''; ?>" /> <br />

  </fieldset>

  <?php if ($myTown || $myCountry): ?>
    <fieldset>
      <?php if ($myTown): ?>
        <input type="checkbox" class="floatLeft" id="myTown" name="f[my_town]" value="1" <?php echo (isset($params['my_town']) && $params['my_town']) ? 'checked="checked"' : ''; ?> />
        <label for="myTown" class="displayInline">Only members in my town (<?php echo $myTown?>):</label>
        <div style="clear:left"></div>
      <?php endif; ?>
      <?php if ($myCountry): ?>
        <input type="checkbox" class="floatLeft" id="myCountry" name="f[my_country]" value="1" <?php echo (isset($params['my_country']) && $params['my_country']) ? 'checked="checked"' : ''; ?> />
        <label for="myCountry" class="displayInline">Only members in my country (<?php echo code_to_country($myCountry); ?>):</label>
        <div style="clear:left"></div>
      <?php endif; ?>
    </fieldset>
  <?php endif; ?>

  <br class="clear" />
  <br />
  <input type="submit" value="Find members" />

</form>

<br />

<h3>Showing <?php echo count($users); ?> members (out of <?php echo $count; ?>)</h3>

<form action="<?php echo url_for('@member_contact_requests'); ?>" method="post">
  <table>
    <thead>
      <tr>
        <th>Select</th>
        <th>Name</th>
        <th>Town</th>
        <th>Country</th>
        <?php if ($myLatLon): ?>
          <th style="text-align: right">Dist</th>
        <?php endif; ?>
        <?php if ($sf_user->getId() && $sf_user->isAdmin()): ?>
          <th>Permissions</th>
        <?php endif; ?>
      </tr>
    </thead>
    <?php if (!count($users)): ?>
      <tbody>
        <tr><td colspan="5">None so far.</td></tr>
      </tbody>
    <?php else: ?>
      <tbody>
        <?php foreach($users as $aUser): ?>
          <tr>
            <td>
              <input type="checkbox" name="members[]" value="<?php echo $aUser->getId(); ?>" <?php if (!$aUser->isRequestable()): ?> disabled="disabled" <?php endif; ?> />
            </td>
            <td><?php echo link_to($aUser->getDisplayName(), '@member_show?username='.$aUser->getUsername()); ?></td>
            <td><?php echo $aUser->getTown(); ?></td>
            <td><?php echo code_to_country($aUser->getCountry()); ?></td>
            <?php if ($myLatLon): ?>
              <td style="text-align: right">
                <?php if ($myLatLon && $aUser->getLatitude() != null && $aUser->getLongitude() != null): ?>
                  <?php echo number_format(PlatformUtils::latLonDist($myLatLon, array($aUser->getLatitude(), $aUser->getLongitude()), 4) / 1000, 1, '.', ','); ?> km
                <?php endif; ?>
              </td>
            <?php endif; ?>
            <?php if ($sf_user->getId() && $sf_user->isAdmin()): ?>
              <td>
                Promote/demote
              </td>
            <?php endif; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    <?php endif; ?>
  </table>
  <input type="submit" value="Send a contact request to selected member(s)" />
</form>