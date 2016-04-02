<?php
namespace tlslib\db;
use Zend\Db\RowGateway\RowGateway;
use Zend\Db\Adapter\Adapter;

/**
 * 
 * @property int $Id   Id
 */
class OpensslEntity extends RowGateway
{
	/**
	 * 
	 * @param Adapter $adapter
	 */
	public function __construct (Adapter $adapter)
	{
		parent::__construct(array(
				'Id'
		), 'tls_openssl', $adapter);
	}
}

