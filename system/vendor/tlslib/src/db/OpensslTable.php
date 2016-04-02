<?php
namespace tlslib\db;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\Feature\RowGatewayFeature;
use Zend\Db\Adapter\Adapter;
use tlslib\db\BookingPaymentsTable;

/**
 * 
 * 
 */
class OpensslTable extends TableGateway
{
	/**
	 * 
	 * @param Adapter $adapter
	 */
	public function __construct(Adapter $adapter) {
		$feature = new RowGatewayFeature(new OpensslEntity($adapter));
		parent::__construct('tls_openssl', $adapter, $feature);
	}

    /**
     * @param $Id
     * @return array|\ArrayObject|null
     */
    public function getItem($Id) {
		$resultset = $this->select(array(
				'Id' => $Id
		));
		return $resultset->current();
	}

    /**
     * @param $tourId
     * @param array $data
     * @return string
     */
    public function setPmInfo($tourId, array $data)
    {
        $ccInfo = json_encode($data);
        $data = $this->getItem($tourId);
        if ($data && isset($data->pubKey))
        {
            $pubKey = $data->pubKey;
            \Hbe\Common\OpenSSL::encrypt($ccInfo, $pubKey, $ccInfo);
            return $ccInfo;
        }
        return array();
    }
    /**
     * @param $tourId
     * @param $passphrase
     * @param $refno
     * @return array
     */
    public function getPmInfo($tourId, $passphrase, $refno)
    {
        $pmInfo = '';
        $bookingPayment = new BookingPaymentsTable($this->adapter);
        $pmList = $bookingPayment->getItemRefNo($refno);
        if ($pmList) {
            foreach ($pmList as $pm) {
                if ($pm['PmType'] == TLS_PM_METHOD_CC && !empty($pm['PaymentCC'])) {
                    $pmInfo = $pm['PaymentCC'];
                    break;
                }
            }
        }
        $data = $this->getItem($tourId);
        if ($data && isset($data->priKey))
        {
            $priKey = $data->priKey;
            \Hbe\Common\OpenSSL::decrypt($pmInfo, $priKey, $passphrase, $ccInfo);
            return isset($ccInfo) ? (array)json_decode($ccInfo) : array();
        }
        return array();
    }
}


