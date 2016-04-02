<?php 
namespace tlslib\db;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class OpensslEntityFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$adapter = $serviceLocator->get('dbadapter');
		$instance = new OpensslEntity($adapter);
	
		return $instance;
	}
}

