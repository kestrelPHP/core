<?php
namespace tlslib\db;
use Zend\Db\RowGateway\RowGateway;
use Zend\Db\Adapter\Adapter;

/**
 *
 * @property    int			$GuideID    	GuideId
 * @property    int			$OperatorID		OperatorID
 * @property    int			$GuideStatus	GuideStatus
 * @property    tinyint 	$Sex			Sex
 * @property    date		$DOB			DOB
 * @property    varchar 	$FirstName		FirstName
 * @property    varchar 	$LastName		LastName
 * @property    varchar 	$Email			Email
 * @property    varchar 	$Phone			Phone
 */
class GuideEntity extends RowGateway
{
	/**
	 * 
	 * @param Adapter $adapter
	 */
	public function __construct (Adapter $adapter)
	{
		parent::__construct(array(
				'GuideId',
		), TABLE_TOUR_GUIDE, $adapter);
	}
}

