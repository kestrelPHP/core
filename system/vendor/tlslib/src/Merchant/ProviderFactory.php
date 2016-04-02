<?php
namespace tlslib\Merchant;

use Hbe\Booking\Model\Item;
use Hbe\Booking\Model\Payment;
use Hbe\Common\BaseCollection;
use Hbe\Common\BaseObject;
use Hbe\Common\ModelService;
use Hbe\Merchant\Exception\ExceptionInterface;
use Hbe\Merchant\Exception\UnexpectedValueException;
use Hbe\Merchant\Model\Resource\Collection\MerchantProviderSetting as APSettings;
use Hbe\Merchant\Provider\Online\AbstractOnline;
use Hbe\Merchant\Provider\Online\OnlineInterface;
use Hbe\Merchant\Provider\Online\PaymentResult;
use Hbe\Merchant\Model\MerchantProviderSetting;
use tlslib\db\BookingItemsEntity;
use tlslib\db\BookingItemsExtrasEntity;
use tlslib\db\BookingItemsExtrasTable;
use tlslib\db\BookingItemsTable;
use tlslib\db\BookingPaymentsEntity;
use tlslib\db\BookingsEntity;
use tlslib\db\BookingsTable;
use tlslib\Merchant\Model\Booking;
use tlslib\Tls;
use tlslib\Operator\Model\Email;

class ProviderFactory extends \Hbe\Merchant\ProviderFactory
{
    /**
     * Get AP Settings
     *
     * @param $opId
     * @param $code string Provider Code
     * @throws \Exception
     * @throws \RuntimeException
     * @return APSettings
     */
    public static function getAPSettings($opId, $code) {
        try {
            $merchantSettingTable = Tls::get('MerchantSettingTable');
            $operatorTable = Tls::get('TripOperatorsTable');
            $operatorInf = $operatorTable->getItem($opId);
            $merchantSettingDefault = $merchantSettingTable->getParams($code);
            $arrParrams = array();
            foreach ( $merchantSettingDefault as $par ) {
                $arrParrams[$par['Name']] = $par;
            }

            $providerSettings = new \tlslib\Merchant\Model\Resource\Collection\MerchantProviderSetting();
            $providerSettings->setModelItems($merchantSettingDefault, '', 'Name');
            $listPayments = $operatorInf->Payments ? json_decode($operatorInf->Payments, true) : array();
            $paymentInf = $listPayments[$code];

            // use param default
            foreach ( $providerSettings as &$proSetting ) {
                // if params user input then use
                foreach ( $paymentInf as $inf ) {
                    if ( $proSetting->getParamId() == $inf['ParamId'] ) {
                        $valueType = $proSetting->getValueType();
                        $value = $inf['Value'];
                        if ( $valueType == MerchantProviderSetting::VALUE_TYPE_LONGTEXT ||
                            $valueType == MerchantProviderSetting::VALUE_TYPE_TEXT
                        ) {
                            $settingValue = $value ? $value : array();
                            $translate = $proSetting->getTranslation();
                            if ( $translate == 1 ) {
                                $settingValue = isset($settingValue['en']) ? $settingValue['en'] : '';
                            }
                            else {
                                $settingValue = isset($settingValue[0]) ? $settingValue[0] : '';
                            }
                        }
                        else {
                            if ( $valueType == MerchantProviderSetting::VALUE_TYPE_CURRENCY ) {
                                $settingValue = !empty($value) ? $value : array();
                                $providerSettings->currency = !empty($settingValue[0]) ? $settingValue[0] : '';
                            }
                            else if ( $valueType == MerchantProviderSetting::VALUE_TYPE_SELECT ) {
                                $settingValue = json_decode($value,true);
                                $settingValue = $settingValue[0];

                            }
                            else {
                                $settingValue = $value;
                            }
                        }

                        $proSetting->setValue($settingValue);
                    }
                }
            }

            return $providerSettings;
        }
        catch ( \RuntimeException $ex ) {
            throw $ex;
        }
    }

    /**
     * Get Online Form Request
     *
     * @param $paymentUrl
     * @param $method
     * @param BaseObject $params
     * @return string
     */
    public static function getOnlineFormRequest($paymentUrl, $method, BaseObject $params, $redirect = false) {
        $requestForm = '<form method="' . $method . '" action="' . $paymentUrl . '" name="formPG">';
        $params = $params->getData();
        foreach ( $params as $key => $val ) {
            $requestForm .= '<input type="hidden" name="' . $key . '" value="' . $val . '" />';
        }

        $requestForm .= '</form>';

        return $requestForm;
    }

    /**
     * Process Payment IPN
     *
     * @param BaseObject $response
     * @return \Hbe\Merchant\Provider\Online\PaymentResult
     * @throws \UnexpectedValueException
     * @throws \Exception
     * @throws \RuntimeException
     */
    public static function processOnlineIPN(BaseObject $response) {
        $bookingTable = Tls::get('BookingsTable');
        $bookingPayment = Tls::get('BookingPaymentsTable');
        $code = trim($response->getData('pg'));
        if ( !$code && $response->getData('pg_refno') ) {
            $code = explode('|', $response->getData('pg_refno'));
            $code = $code[0];
        }

        try {
            // Log Raw Payment
            //self::_logPayment($response, $code);
            //- Validate Payment gateway
            /** @var $provider OnlineInterface */
            if ( !$code || (!$provider = ModelService::get("Payment\\{$code}")) ) {
                throw new UnexpectedValueException("Invalid [{$code}].");
            }

            //- Get booking info
            $refNo = $provider->getPaymentBookingId($response);
            if ( !$refNo ) {
                throw new UnexpectedValueException("Invalid RefNo [Empty].");
            }

            $refNo = explode('-', $refNo);
            $refNo = count($refNo) > 1 ? $refNo[1] : reset($refNo);

            $arrRefNo = explode('|', $refNo);
            $booking = new Booking($refNo);

            // Convert $arrRefNo (ExtRefNo) to RefNo (not prefix)
            $RefNoNonPrefix = array();
            if(count($arrRefNo) > 0){
                foreach($arrRefNo as $id) {
                    $ref = substr($id, 2, strlen($id));
                    $RefNoNonPrefix[] = $ref;
                }
            }
            $bookingsEntity = $bookingTable->getItems($RefNoNonPrefix);

            if ( !$refNo || !$bookingsEntity ) {
                throw new UnexpectedValueException("Invalid RefNo [{$refNo}].");
            }

            // Log IPN Request
            $log = $response->getData();

            //- Process Payment
            $apSettings = self::getAPSettings($booking->OperatorID, $code);
            $paymentResult = $provider->getPaymentResponse($response, $booking, $apSettings);
            // If Payment has flag status that indicate End, don't process at all.
            if ( $paymentResult->getAction() == AbstractOnline::ACT_END ) {
                if ( $paymentResult->getStatus() == Payment::PS_ERROR || $paymentResult->getError() ) {
                    throw new UnexpectedValueException($paymentResult->getError(), Payment::PS_ERROR);
                }

                return $paymentResult;
            }
            else if ( $paymentResult->getAction() == AbstractOnline::ACT_REDIRECT
                && !$paymentResult->getAmount()
            ) {
                // For some payments that verified while IPN
                //$paymentResult->setStatus($bookingEntity->BkStatus == $booking::BKS_CN ? Payment::PS_RECEIVED : Payment::PS_ERROR);
                foreach ( $bookingsEntity as $bk ) {
                	if ( $bk->BkStatus == $booking::BKS_RQ ) {
                	    continue;
                	}

                	$paymentResult->setStatus($bk->BkStatus == $booking::BKS_CN ? Payment::PS_RECEIVED : Payment::PS_ERROR);
                }

                return $paymentResult;
            }
            // Update status Payments
            foreach ( $RefNoNonPrefix as $rn ) {
                /** @var BookingPaymentsEntity $payment */
                $payment = $bookingPayment->getItemRefNoCode($rn, $code);
                if ( $payment ) {
                    $payment->PmStatus = $paymentResult->getStatus();
                    $payment->RequiredAmt = $booking->getDeposit();
                    //$payment->ReceivedAmt = $paymentResult->getAmount();
                    $payment->Logs        = json_encode($log);
                    $payment->save();
                }
            }

            // Submit Booking (Hold Alm) if not
            $paySucceed = false;
            if ( in_array($paymentResult->getStatus(), $provider->getConfirmedStates()) ) {
                $paySucceed = true;
            }

            // Update status bookings
            foreach ( $bookingsEntity as $bk ) {
                /** @var $bk BookingsEntity */
                // Not Update booking OnRequest
                if ( $bk->BkStatus == $booking::BKS_RQ ) {
                    continue;
                }

                if ( $paySucceed ) {
                    if ( $bk->BkStatus == Booking::BKS_IN  && in_array($paymentResult->getStatus(), $provider->getConfirmedStates())) {
                        $bk->BkStatus = Booking::BKS_CN;
                        $bk->save();
                        // Update status booking items
                        /** @var BookingItemsTable $BookingItemsTable */
                        $BookingItemsTable = Tls::get('BookingItemsTable');
                        $items = $BookingItemsTable->getItems($bk->RefNo);
                        if($items){
                            foreach($items as $item){
                                /** @var $itemInfo BookingItemsEntity */
                                $itemInfo = $BookingItemsTable->getItem($item['BkItemID']);
                                if($itemInfo){
                                    $itemInfo->BkItemStatus = Item::BKI_CN;
                                    $itemInfo->save();
                                }
                            }
                        }
                        // If has Extras
                        /** @var BookingItemsExtrasTable $bookingItemsExtraTable */
                        $bookingItemsExtraTable = Tls::get('BookingItemsExtrasTable');
                        $ExtrasItems = $bookingItemsExtraTable->getItems($bk->RefNo);
                        if($ExtrasItems){
                            foreach($ExtrasItems as $extra){
                                /** @var BookingItemsExtrasEntity $extraInfo */
                                $extraInfo = $bookingItemsExtraTable->getItem($extra['BkItemExtID']);
                                $extraInfo->BkItemStatus = Item::BKI_CN;
                                $extraInfo->save();
                            }
                        }
                        // Update alloment
                        /** @var BookingsTable $bookingTable */
                        $bookingTable->submitAllotment($bk->RefNo);
                    }
                }
            }
            // Notify to traveller ...
            if ( in_array($paymentResult->getStatus(), $provider->getConfirmedStates()) ) {
                foreach($RefNoNonPrefix as $ref) {
                    //Send mail
                    $classEmail = new Email();
                    $classEmail->sendMail(
                        $ref,
                        array(
                            'CorrId' => TLS_CORR_BK_CONFIRM_TO_TRAVELLER,
                            'Traveller' => true,
                            'lang' => 'en',
                            'attachment' => true
                        )
                    );
                }
            }

            return $paymentResult;
        }
        catch ( ExceptionInterface $ex ) {
            return self::_initErrorPaymentResult(
                $ex->getMessage(),
                isset($refNo) ? $refNo : null,
                isset($provider) ? $provider : !isset($refNo) ? AbstractOnline::ACT_END : AbstractOnline::ACT_REDIRECT
            );
        }
    }

    /**
     * @param $message
     * @param null $refNo
     * @param null $provider
     * @param int $action
     * @return PaymentResult
     */
    protected static function _initErrorPaymentResult(
        $message, $refNo = null, $provider = null,
        $action = AbstractOnline::ACT_REDIRECT
    ) {
        $paymentResult = new PaymentResult($refNo, Payment::PS_ERROR);
        $paymentResult->setAction($action)
            ->setProvider($provider)
            ->setError($message);

        return $paymentResult;
    }
}