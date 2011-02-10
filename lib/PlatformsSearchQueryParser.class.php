<?php

/**
 * PlatformsSearchQueryParser
 *
 * Import default data from json file into the database.
 *
 * origin: M
 *
 * @package    platforms
 * @subpackage lib
 * @version    SVN: $Id: $
 */
class PlatformsSearchQueryParser
{
  private $context;

  public function __construct(mwContext $context)
  {
    $this->context = $context;
  }

  public function parse($query)
  {
    return array(
      'cats' => $this->translateCategoryNamesToIds($this->parseCategoryNames($query)),
      'term' => $this->parseQueryTerm($query),
      'date_start' => $this->parseFrom($query),
      'date_end' => $this->parseUntil($query),
    );
  }

  public function parseCategoryNames($query)
  {
    $categoryNames = null;
    preg_match_all('/\bcat:([\w\d]+)\b/u', $query, $categoryNames);
    return array_unique($categoryNames[1]);
  }

  public function translateCategoryNamesToIds(array $categoryNames)
  {
    return $this->getDoctrineTable('Category')->translateCategoryNamesToIds($categoryNames);
  }

  public function parseQueryTerm($query)
  {
    $queryTerms = null;
    $query = preg_replace('/\b[\w\d]+:([\w\d]+)\b/u', ' ', $query); // hotfix: remove prefixed terms
    preg_match_all('/\s+([\w\d]+)\b/u', ' '.$query.' ', $queryTerms);
    return join(' ', array_unique($queryTerms[1]));
  }

  /**
   * Convenience method to fetch table class for given model name.
   *
   * @param string $modelName
   * @return Doctrine_Table
   */
  private function getDoctrineTable($modelName)
  {
    return $this->context->getDoctrineTable($modelName);
  }

  public function parseFrom($query)
  {
    $from = null;
    preg_match('/\bfrom:(\d{8})\b/u', $query, $from);
    return isset($from[1]) ? PlatformUtils::isoToDate($from[1]) : null;
  }

  public function parseUntil($query)
  {
    $until = null;
    preg_match('/\buntil:(\d{8})\b/u', $query, $until);
    return isset($until[1]) ? PlatformUtils::isoToDate($until[1]) : null;
  }
}