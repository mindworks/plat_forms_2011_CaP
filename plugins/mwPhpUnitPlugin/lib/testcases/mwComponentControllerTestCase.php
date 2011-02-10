<?php

/**
 * Abstract base test case for symfony component tests.
 *
 * origin: RM
 *
 * @package    mwPhpUnitPlugin
 * @subpackage testcases
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: mwComponentControllerTestCase.php 1051 2010-12-16 14:02:15Z mi_mmeyer $
 */
abstract class mwComponentControllerTestCase extends mwPhpUnitTestCase
{
  /**
   * @var sfWebRequest RequestMock
   */
  protected $request;

  /**
   * @var sfWebResponse ResponseMock
   */
  protected $resonse;

  /**
   * @var projectContext ContextMock
   */
  protected $context;

  /**
   * @var sfController ControllerMock
   */
  protected $controller;

  /**
   * @var projectUser UserMock
   */
  protected $user;

  /**
   * @var array Associative array of "model name" => "table mock"
   */
  private $tables = array();

  /**
   * @var array Associative array of "request data key" => "request data value"
   */
  private $requestData = array();

  /**
   * @var array Associative array of "model name" => "doctrine record mock"
   */
  private $modelCreationMap = array();

  /**
   * @var array Associative array of "model name" => "doctrine form mock"
   */
  private $forms = array();

  /**
   * Prepares the environment before running a test.
   *
   * @todo move user::can() anf getGuardUser() stubs to subclass
   */
  protected function setUp()
  {
    parent::setUp();

    $this->context = $this->getMockWithoutCallingConstructor('projectContext');

    $this->request = $this->getMockWithoutCallingConstructor('sfWebRequest');
    $this->context->expects($this->any())
      ->method('getRequest')
      ->will($this->returnValue($this->request));

    $this->request->expects($this->any())
      ->method('getParameter')
      ->will($this->returnCallback(array($this, 'getParameterCallback')));

    $this->response = $this->getMockWithoutCallingConstructor('sfWebResponse');
    $this->context->expects($this->any())
      ->method('getResponse')
      ->will($this->returnValue($this->response));

    $this->controller = $this->getMockWithoutCallingConstructor('sfController');
    $this->context->expects($this->any())
      ->method('getController')
      ->will($this->returnValue($this->controller));

    $this->user = $this->getMockWithoutCallingConstructor('projectUser');
    $this->context->expects($this->any())
      ->method('getUser')
      ->will($this->returnValue($this->user));

    $this->context->expects($this->any())
      ->method('getDoctrineTable')
      ->will($this->returnCallback(array($this, 'getTableCallback')));

    $this->context->expects($this->any())
      ->method('createModel')
      ->will($this->returnCallback(array($this, 'createModelCallback')));

    $this->context->expects($this->any())
      ->method('createForm')
      ->will($this->returnCallback(array($this, 'createFormCallback')));
  }

  /**
   * Callback for mocking projectContext::getDoctrineTable()
   *
   * @param string $modelName
   * @return Doctrine_Table Table mock
   */
  public function getTableCallback($modelName)
  {
    return $this->getTable($modelName);
  }

  /**
   * Prepare tables for projectContext::getDoctrineTable() mocking callback.
   *
   * @todo Add methods to add one and add mutliple tables.
   *
   * @param array $tables Associative array of "model name" => "table mock"
   */
  protected function setTablesForGetDoctrineTable(array $tables)
  {
    $this->tables = $tables;
  }

  /**
   * Get preconfigured table mock or create one on the fly (from explicit table
   * class if it exists, from "Doctrine_Table" otherwise).
   *
   * @param string $modelName
   * @return PHPUnit_Framework_MockObject_Mock
   */
  protected function getTable($modelName)
  {
    if(!isset($this->tables[$modelName]))
    {
      $tableClass = $modelName.'Table';
      if(!class_exists($modelName.'Table'))
      {
        $tableClass = 'Doctrine_Table';
      }
      $this->tables[$modelName] = $this->getMockWithoutCallingConstructor($tableClass);
    }
    return $this->tables[$modelName];
  }

  /**
   * Get preconfigured form mock or create one on the fly.
   *
   * @param string $formClass
   * @return PHPUnit_Framework_MockObject_Mock
   */
  protected function getForm($formClass)
  {
    if(!isset($this->forms[$formClass]))
    {
      if(!class_exists($formClass))
      {
        throw new RuntimeException(sprintf('Form "%s" does not exist.', $formClass));
      }
      $this->forms[$formClass] = $this->getMockWithoutCallingConstructor($formClass);
    }
    return $this->forms[$formClass];
  }

  /**
   * Add an invovation expectation for Doctrine_Table mock.
   *
   * @todo refactor
   *
   * @param string $modelName
   * @param array $options
   * @return PHPUnit_Framework_MockObject_Mock
   */
  protected function setExpectationForTable($modelName, array $options)
  {
    $tableMock = $this->getTable($modelName);

    if(!isset($options['method']))
    {
      $options['method'] = 'find';
    }

    $expects = $this->once();
    if(isset($options['expects']))
    {
      $expects = $options['expects'];
    }

    if(!method_exists($tableMock, $options['method']))
    {
      $originalMethod = $options['method'];
      $options['method'] = '__call';
      $invocationMock = $tableMock->expects($expects)->method($options['method']);
      if(isset($options['with']))
      {
        $invocationMock->with($this->equalTo($originalMethod), $this->equalTo(array($options['with'])));
      }
    }
    else
    {
      $invocationMock = $tableMock->expects($expects)->method($options['method']);
      if(isset($options['with']))
      {
        $invocationMock->with($this->equalTo($options['with']));
      }
    }

    if(!isset($options['returnValue']))
    {
      $options['returnValue'] = false;
    }

    $invocationMock->will($this->returnValue($options['returnValue']));

    return $tableMock;
  }

  /**
   * Add an invovation expectation for BaseDoctrineForm mock.
   *
   * @todo add bind expectation to options
   *
   * @param string $formClass
   * @param mwDoctrineRecordMock $object
   * @param array $options
   */
  protected function setExpectationForForm($formClass, mwDoctrineRecordMock $object = null, array $options = array())
  {
    $form = $this->getForm($formClass);

    $form->expects($this->any())
      ->method('getObject')
      ->will($this->returnValue($object));

    if(isset($options['isValid']))
    {
      $form->expects($this->once())
        ->method('isValid')
        ->will($this->returnValue($options['isValid']));
    }

    if(isset($options['expectSave']))
    {
      $form->expects($this->once())
        ->method('save');
    }
  }

  /**
   * Callback for mocking projectContext::getDoctrineTable()
   *
   * @param string $key Request data key
   * @return mixed Request value
   */
  public function getParameterCallback($key)
  {
    return isset($this->requestData[$key]) ? $this->requestData[$key] : false;
  }

  /**
   * Prepare request data for sfWebRequest::getParameter() mocking callback.
   *
   * @todo Add methods to add one and add mutliple values.
   *
   * @param array $tables Associative array of "request data key" => "request data value"
   */
  protected function setRequestDataForGetParameter(array $requestData)
  {
    $this->requestData = $requestData;
  }

  /**
   * Callback for mocking projectContext::getDoctrineTable()
   *
   * @param string $modelName
   * @return mwDoctrineRecordMock
   */
  public function createModelCallback($modelName)
  {
    return isset($this->modelCreationMap[$modelName]) ? $this->modelCreationMap[$modelName] : new mwDoctrineRecordMock();
  }

  /**
   * Callback for mocking projectContext::getDoctrineTable()
   *
   * @param string $formClass
   * @return BaseDoctrineForm mock
   */
  public function createFormCallback($formClass)
  {
    return $this->getForm($formClass);
  }

  /**
   * Prepare map for projectContext::createModel() mocking callback.
   *
   * @todo Add methods to add one and add mutliple values.
   *
   * @param array $modelCreationMap Associative array of "model name" => "doctrine record mock"
   */
  protected function setModelCreationMap(array $modelCreationMap)
  {
    $this->modelCreationMap = $modelCreationMap;
  }


  /**
   * Prepare map for projectContext::createForm() mocking callback.
   *
   * @todo Add methods to add one and add mutliple values.
   *
   * @param array $forms Associative array of "model name" => "doctrine form mock"
   */
  protected function setFormsForCreateFormCallback(array $forms)
  {
    $this->forms = $forms;
  }

  /**
   * Sets up controller to expect a certain redirect.
   *
   * @param string $url
   */
  protected function setExpectedRedirect($url, $expectException = true)
  {
    $this->controller->expects($this->once())
      ->method('__call')
      ->with($this->equalTo('redirect'), $this->equalTo(array($url, 0, 302)));

    if($expectException)
    {
      $this->setExpectedException('sfStopException');
    }
  }
}