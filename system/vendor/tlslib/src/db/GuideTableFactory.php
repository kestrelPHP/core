<?php 
namespace tlslib\db;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class GuideTableFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$adapter = $serviceLocator->get('dbadapter');
		$instance = new GuideTable($adapter);
	
		return $instance;
	}
}

