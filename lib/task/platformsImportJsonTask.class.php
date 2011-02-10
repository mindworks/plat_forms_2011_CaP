<?php

/**
 * Import json task
 *
 * Import default data from json file into the database.
 *
 * origin: M
 *
 * @package    platforms
 * @subpackage lib
 * @version    SVN: $Id: $
 */
class platformsImportJsonTask extends sfDoctrineBaseTask
{
  /**
   * Configure task
   */
  protected function configure()
  {
    $this->namespace = 'platforms';
    $this->name = 'import-json';
    $this->briefDescription = 'Import default data from json file into the database.';
    $this->addArguments(array(
      new sfCommandArgument('environmentname', sfCommandArgument::OPTIONAL)
    ));
  }

  /**
   * Execute task
   *
   * @param array $arguments
   * @param array $options
   */
  protected function execute($arguments = array(), $options = array())
  {
    if (isset($arguments['environmentname']))
    {
      $env = $arguments['environmentname'];
    }
    else
    {
      $env = 'dev';
    }
    $applicationName = 'frontend';
    $configuration = ProjectConfiguration::getApplicationConfiguration($applicationName, $env, true);
    $context = sfContext::createInstance($configuration, null, 'mwContext');
    $this->setConfiguration($configuration);

    $dataFileName = 'data/json/data.txt';
    if (is_readable($dataFileName)) {
      $jsonString = '['.file_get_contents($dataFileName).']';
    }
    else {
      throw new RuntimeException('File ['.$dataFileName.'] does not exists.');
    }
    $importer = new PlatformsDataImporter($context);
    $importer->importAll($jsonString);
  }

  private function importJson($jsonString)
  {
    echo 'Decoding';
    $result = json_decode($jsonString, true);
    var_dump($result);

    $lastError = json_last_error();
    switch($lastError)
    {
        case JSON_ERROR_DEPTH:
            echo ' - Maximum stack depth exceeded';
        break;
        case JSON_ERROR_CTRL_CHAR:
            echo ' - Unexpected control character found';
        break;
        case JSON_ERROR_SYNTAX:
            echo ' - Syntax error, malformed JSON';
        break;
        case JSON_ERROR_NONE:
            echo ' - No errors';
        break;
        default:
          echo ' - '.$lastError;
    }

    echo PHP_EOL;
  }
}