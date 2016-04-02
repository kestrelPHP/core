<?php 
namespace tlslib\db;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class GuideEntityFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$adapter = $serviceLocator->get('dbadapter');
		$instance = new GuideEntity($adapter);
	
		return $instance;
	}
}

