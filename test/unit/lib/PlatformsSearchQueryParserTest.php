<?php

//require_once 'apps/frontend/lib/PlatformsUtils.class.php';

/**
 * PlatformsSearchQueryParser test case.
 *
 * origin: M
 *
 * @package    platforms
 * @subpackage test
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */
class PlatformsSearchQueryParserTest extends mwPhpUnitTestCase
{

  /**
   * @var PlatformsSearchQueryParser
   */
  private $platformsSearchQueryParser;

  /**
   * @var string
   */
  private $query;

  /**
   * @var mwTableMock
   */
  private $tableMock;

  /**
   * Prepares the environment before running a test.
   */
  protected function setUp()
  {
    parent::setUp();

    $this->context = $this->getMockWithoutCallingConstructor('mwContext');
    $this->tableMock = new mwTableMock();
    $this->context->expects($this->any())
      ->method('getDoctrineTable')
      ->will($this->returnValue($this->tableMock));

    $this->platformsSearchQueryParser = new PlatformsSearchQueryParser($this->context);
    $this->query = 'yyy cat:cat1 cat:cät2 üüü cat:cat1 xxx xxx YYY from:2011-01-01 until:2011-02-01';
  }

  /**
   * Cleans up the environment after running a test.
   */
  protected function tearDown()
  {
    $this->platformsSearchQueryParser = null;

    parent::tearDown();
  }

  /**
   * Tests PlatformsSearchQueryParser->parse()
   */
  public function testParse()
  {
    $categoryIds = array(1, 2, 666);
    $this->tableMock->registerCallMock('translateCategoryNamesToIds', $categoryIds);
    $searchParams = $this->platformsSearchQueryParser->parse($this->query);
    $expected = array(
      'cats' => $categoryIds,
      'term' => 'yyy üüü xxx YYY',
    );
    $this->assertEquals($expected['cats'], $searchParams['cats']);
    $this->assertEquals($expected['term'], $searchParams['term']);
  }

  /**
   * Tests PlatformsSearchQueryParser->parseCategoryNames()
   */
  public function testParseCategoryNames()
  {
    $categories = $this->platformsSearchQueryParser->parseCategoryNames($this->query);
    $this->assertEquals(array('cat1', 'cät2'), $categories);
  }


  /**
   * Tests PlatformsSearchQueryParser->parseQueryTerm()
   */
  public function testParseQueryTerm()
  {
    $terms = $this->platformsSearchQueryParser->parseQueryTerm($this->query);
    $this->assertEquals('yyy üüü xxx YYY', $terms);
  }
}