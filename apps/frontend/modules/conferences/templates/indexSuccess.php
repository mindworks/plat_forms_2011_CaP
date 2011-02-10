<?php
/**
 * conferences index template.
 *
 * origin: M
 *
 * @package    platforms
 * @subpackage conferences
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */
?>

<?php $rawParams = $sf_data->getRaw('params'); ?>

<h2>Search for conferences</h2>

<form action="?" method="get" class="searchPanel">

  <fieldset>

    <label for="query">Query:</label>
    <input type="text" name="f[term]" id="query" value="<?php echo isset($params['term']) ? $params['term'] : ''; ?>" /><br />

    <label for="date_start">Start date:</label>
    <input type="text" name="f[date_start]" id="date_start" value="<?php echo isset($params['date_start']) ? $params['date_start'] : ''; ?>" /><br />

    <label for="date_end">End date:</label>
    <input type="text" name="f[date_end]" id="date_end" value="<?php echo isset($params['date_end']) ? $params['date_end'] : ''; ?>" /><br />

  </fieldset>

  <fieldset>

    <?php if (count($countries)): ?>
      <label for="countries">Regions:</label>
      <select id="countries" name="f[regions][]" multiple="multiple" style="height: 200px;">
        <?php foreach ($countries as $cc => $country): ?>
          <option
            value="<?php echo $cc; ?>"
            <?php if (isset($rawParams['regions']) && is_array($rawParams['regions']) && in_array($cc, $rawParams['regions'])): ?>selected="selected"<?php endif; ?>
          ><?php echo $country; ?></option>
        <?php endforeach; ?>
      </select>
    <?php endif; ?>

    <?php if ($myCountry): ?>
      <label for="country_only">Limit results to my country (<?php echo $myCountry?>):</label>
      <input type="checkbox" id="country_only" name="f[country_only]" value="<?php echo $myCountry; ?>" <?php echo (isset($params['country_only']) && $params['country_only']) ? 'checked="checked"' : ''; ?> /><br />
    <?php endif; ?>

  </fieldset>

  <fieldset>

    <?php if (count($categories)): ?>
      <label for="cats">Categories:</label>
      <select id="cats" name="f[cats][]" multiple="multiple" style="height: 200px;">
        <?php foreach ($categories as $category): ?>
          <option
            value="<?php echo $category->getId(); ?>"
            <?php if (isset($rawParams['cats']) && is_array($rawParams['cats']) && in_array($category->getId(), $rawParams['cats'])): ?>selected="selected"<?php endif; ?>
          ><?php echo $category->getName(); ?></option>
        <?php endforeach; ?>
      </select>
    <?php endif; ?>

  </fieldset>

  <br class="clear" />

  <input type="submit" value="Find conferences" />

  <br /> <br />
  <p>If you leave the 'Start date' filter blank, only running conferences or conferences starting later than the current date are displayed.</p>

</form>

<form action="<?php echo url_for('@conferences_expert_search')?>" method="get" class="searchPanel">

    <label for="query">Expert query:</label>
    <input type="text" name="query" id="query" style="width: 400px;" value="<?php echo isset($params['expert_term']) ? $params['expert_term'] : ''; ?>" />
    <input type="submit" value="Find conferences" />
</form>

<br />

<h3>Showing <?php echo count($conferences); ?> conferences (out of <?php echo $count; ?>)</h3>

<table>
  <thead>
    <tr>
      <th>Name of the conference</th>
      <th>Date</th>
      <th>Location</th>
      <?php if ($sf_user->isAuthenticated()): ?>
        <th>Personal calendar</th>
      <?php endif; ?>
    </tr>
  </thead>
  <?php if (!count($conferences)): ?>
    <tbody>
      <tr><td colspan="4">None so far.</td></tr>
    </tbody>
  <?php else: ?>
  <tbody>
    <?php foreach ($conferences as $conference): ?>
      <tr>
        <td>
          <?php echo link_to($conference->getName(), '@conference_show?id='.$conference->getId()); ?>
        </td>
        <td>
          <?php echo $conference->getStartdate(); ?> - <?php echo $conference->getEnddate(); ?>
        </td>
        <td>
          <?php echo $conference->getLocation(); ?>
        </td>
        <?php if ($sf_user->isAuthenticated()): ?>
          <?php $conferencesOnCalendar = $sf_data->getRaw('sf_user')->getGuardUser()->getDefaultCalendarItems()->getPrimaryKeys(); ?>
          <td>
            <?php if (in_array($conference->getId(), $conferencesOnCalendar)): ?>
              This conference is on your personal calendar already. <a href="<?php echo url_for('@conference_remove_from_calendar?id='.(integer)$conference->getId()); ?>">Remove it</a>
            <?php else: ?>
              <a href="<?php echo url_for('@conference_add_to_calendar?id='.(integer)$conference->getId()); ?>">Add</a> this conference to my calendar.
            <?php endif; ?>
          </td>
        <?php endif; ?>
      </tr>
    <?php endforeach; ?>
  </tbody>
  <?php endif; ?>
</table>

