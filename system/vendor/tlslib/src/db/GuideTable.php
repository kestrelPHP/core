<?php
namespace tlslib\db;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\Feature\RowGatewayFeature;
use Zend\Db\Adapter\Adapter;

/**
 *
 *
 */
class GuideTable extends TableGateway
{
	/**
	 *
	 * @param Adapter $adapter
	 */
	public function __construct(Adapter $adapter) {
		$feature = new RowGatewayFeature(new GuideEntity($adapter));
		parent::__construct(TABLE_TOUR_GUIDE, $adapter, $feature);
	}

	/**
	 *
	 * @param int $ID GuideId
	 * @return Ambigous <multitype:, ArrayObject, NULL>|boolean
	 */
	public function getItem($ID) {
		$resultset = $this->select(array(
				'GuideId' => $ID,
		));
		return $resultset->current();
	}

    /**
     * @param array $data
     * @return int
     */
	public function insertItem($data){
		$result = $this->insert($data);
		return $result;
	}

    /**
     * @param array $data
     * @param int $key GuideId
     * @return int
     */
	public function updateItem($data, $key){
		return $this->update($data, array('GuideId' => $key));
	}

    /**
     * @param int $key GuideId
     * @return int
     */
    public function deleteItem($key){
        return $this->delete(array('GuideId' => $key));
    }
}


