<?php 
namespace tlslib\db;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class OpensslTableFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$adapter = $serviceLocator->get('dbadapter');
		$instance = new OpensslTable($adapter);
	
		return $instance;
	}
}

