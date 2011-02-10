<?php

/**
 * admin actions.
 *
 * origin: GM
 *
 * @package    platforms
 * @subpackage members
 * @author     Joerg Basedow <jbasedow@mindworks.de>
 * @version    SVN: $Id: $
 */
class adminActions extends mwActions
{
	public function executeWsNotImplemented(sfWebRequest $request)
  {
  	return $this->renderText('');
  }
	
	public function executeWsReset(sfWebRequest $request)
  {
  	if ($this->isApiCall) {
  		if ($this->getUser()->isAdmin()) {
  			
  			$this->resetSystem();
        
  		  header('HTTP/1.0 204 No Content');
        exit;
  		}
  		else {
  			header('HTTP/1.0 403 Forbidden');
        exit;
  		}
  	}
  }
  
  public function executeWsFactoryDefaults(sfWebRequest $request)
  {
    if ($this->isApiCall) {
      if ($this->getUser()->isAdmin()) {
        
      	$this->resetSystem(true);
      	
        header('HTTP/1.0 204 No Content');
        exit;
      }
      else {
        header('HTTP/1.0 403 Forbidden');
        exit;
      }
    }
  }
  
  private function resetSystem($loadJson = false)
  {
  	$env = (isset($_SERVER['ENVIRONMENT']) && $_SERVER['ENVIRONMENT']) ? $_SERVER['ENVIRONMENT'] : 'prod';
        
    $path    = realpath(getcwd().'/..');
        
    $command = $path.'/symfony doctrine:drop-db --env='.$env.' --no-confirmation';
    system($command, $ret);
      
    $command = $path.'/symfony doctrine:build-db --env='.$env;
    system($command, $ret);
      
    $command = $path.'/symfony doctrine:insert-sql --env='.$env;
    system($command, $ret);
      
    $command = $path.'/symfony doctrine:data-load --env='.$env;
    system($command, $ret);
    
    if ($loadJson) {
    	$command = $path.'/symfony platforms:import-json '.$env;
      system($command, $ret);
    }
  }
}
