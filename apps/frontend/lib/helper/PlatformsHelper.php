<?php
/**
 * Platforms view helper for unit tests
 *
 * origin: M
 *
 * @package    platforms
 * @subpackage lib
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */


function code_to_country($code)
{
  return PlatformUtils::codeToCountry($code);
}