<?php

require_once 'apps/frontend/lib/PlatformsUtils.class.php';

/**
 * PlatformsDataImporter test case.
 *
 * origin: M
 *
 * @package    platforms
 * @subpackage test
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */
class PlatformsDataImporterTest extends mwPhpUnitTestCase
{

  /**
   * @var PlatformsDataImporter
   */
  private $platformsDataImporter;

  /**
   * @var array Associative array of "model name" => "doctrine record mock"
   */
  private $modelCreationMap = array();

  /**
   * @var mwTableMock
   */
  private $tableMock;

  /**
   * @var mwContext
   */
  private $context;

  /**
   * Prepares the environment before running a test.
   */
  protected function setUp()
  {
    parent::setUp();

    $this->context = $this->getMockWithoutCallingConstructor('mwContext');
    $this->context->expects($this->any())
      ->method('createModel')
      ->will($this->returnCallback(array($this, 'createModelCallback')));
    $this->tableMock = new mwTableMock();
    $this->context->expects($this->any())
      ->method('getDoctrineTable')
      ->will($this->returnValue($this->tableMock));

    $this->platformsDataImporter = new PlatformsDataImporter($this->context);

  }

  /**
   * Cleans up the environment after running a test.
   */
  protected function tearDown()
  {
    $this->platformsDataImporter = null;

    parent::tearDown();
  }

  /**
   * Tests PlatformsDataImporter->splitToEntityCollections()
   */
  public function testSplitToEntityCollections()
  {
    $json = '[[{"username" : "bgates"}],'.
      '[{"name" : "Arts"}],'.
      '[{"name" : "ICSE"}],'.
      '[{"name" : "26C3 - Here Be Dragons"}]]';
    $allData = json_decode($json, true);

    $dataArray = $this->platformsDataImporter->splitToEntityCollections($allData);

    $this->assertEquals('bgates', $dataArray['users'][0]['username']);
    $this->assertEquals('Arts', $dataArray['categories'][0]['name']);
    $this->assertEquals('ICSE', $dataArray['series'][0]['name']);
    $this->assertEquals('26C3 - Here Be Dragons', $dataArray['conferences'][0]['name']);
  }

  /**
   * Tests PlatformsDataImporter->importCategory()
   */
  public function testImportCategoryNew()
  {
    $json = '{
      "name" : "Arts",
      "subcategories" : [],
      "parent" : {}
    }';
    $categoryData = json_decode($json, true);

    $category = $this->platformsDataImporter->importCategory($categoryData);

    $this->assertEquals('Arts', $category->getName());
    $this->assertTrue($category->isSaved());
  }

  /**
   * Tests PlatformsDataImporter->importCategory()
   */
  public function testImportCategoryExisting()
  {
    $json = '{
      "name" : "Arts",
      "subcategories" : [],
      "parent" : {}
    }';
    $categoryData = json_decode($json, true);
    $this->tableMock->registerCallMock('findOneByName', 'categoryMock');

    $this->platformsDataImporter->importCategory($categoryData);
    $this->assertFalse(isset($this->modelCreationMap['Category']));
  }

  /**
   * Tests PlatformsDataImporter->importCategory()
   */
  public function testImportCategoryWithParent()
  {
    $json = '{
      "name" : "Synthetic Biology",
      "subcategories" : [],
      "parent" : {
        "name" : "Life Science"
      }
    }';
    $categoryData = json_decode($json, true);

    $category = $this->platformsDataImporter->importCategory($categoryData);

    $this->assertEquals('Synthetic Biology', $category->getName());
    $this->assertTrue($category->isSaved());
    $this->assertEquals('Life Science', $category->getParent()->getName());
    $this->assertTrue($category->getParent()->isSaved());
  }

  /**
   * Tests PlatformsDataImporter->importUser()
   */
  public function testImportUser()
  {
    $json = '{
      "username" : "bgates",
      "password" : "kzr",
      "fullname" : "Bill Gates",
      "email" : "bill.gates@example.com",
      "town" : "Seattle",
      "country" : "United States",
      "gps" : "47.36N,122.19W"
    }';
    $userData = json_decode($json, true);
    $user = $this->platformsDataImporter->importUser($userData);

  }

  /**
   * Callback for mocking projectContext::getDoctrineTable()
   *
   * @param string $modelName
   * @return mwDoctrineRecordMock
   */
  public function createModelCallback($modelName)
  {
    return new mwDoctrineRecordMock();
  }
}