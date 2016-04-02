<?php
namespace tlslib\Operator\Model;

use tlslib\db\BkconditionsTable;
use tlslib\db\BookingItemsExtrasEntity;
use tlslib\db\BookingItemsExtrasTable;
use tlslib\db\ExtrasItemsEntity;
use tlslib\db\ExtrasItemsTable;
use tlslib\db\MerchantSettingTable;
use tlslib\db\TripContentsTable;
use tlslib\Tls;
use tlslib\db\BookingsTable;
use tlslib\db\BookingItemsTable;
use tlslib\db\BookingTravellersTable;
use tlslib\db\BookingPaymentsTable;
use tlslib\db\TripOperatorsTable;
use tlslib\db\TripItemsTable;
use tlslib\db\TripCurrencyTable;
use tlslib\db\SaleChargesTable;
use Hbe\Common\Util;

class Email
{
    /**
     * Translate resources
     * @var array
     */
    public static $_resources;

    /**
     * Booking information
     */
    public $_booking;

    /**
     * Traveller information
     */
    public $_traveller;

    /**
     * Payment information
     */
    public $_payment;

    /**
     *  Driver
     */
    public $_adapter;

    /**
     * Class
     */
    public $_class;

    /**
     * Other Information
     */
    public $_info;

    public function __construct() {
        $adapter = Tls::get('dbadapter');
        $this->_adapter = $adapter;
    }

    public static function setResources($resources, $lang = 'en') {
        if ( empty(self::$_resources) ) {
            self::$_resources = array();
        }

        self::$_resources[$lang] = $resources;
    }

    public static function getResouces($lang = 'en') {
        if ( !empty(self::$_resources[$lang]) ) {
            return self::$_resources[$lang];
        }

        $resources = array();

        db_set_active('tls-manage');

        $cache = cache_get('be_message_' . $lang);
        if ( !empty($cache) ) {
            $resources = $cache->data;
        }
        else {
            module_load_include('module', 'tls_translation');
            $data = tls_translation_get_data_be($lang, true);

            foreach ( $data as $k => $v ) {
                $resources[$k] = !empty($v['translation']) ? $v['translation'] : $v['source'];
            }
        }

        db_set_active();

        self::setResources($resources, $lang);

        return $resources;
    }

    /**
     * @param $refno
     * @param $lang
     */
    public function loadData($refno, $lang) {
        $bookingTable = new BookingsTable($this->_adapter);
        $bookingItem = new BookingItemsTable($this->_adapter);
        $bookingTravel = new BookingTravellersTable($this->_adapter);
        $bookingPayment = new BookingPaymentsTable($this->_adapter);
        $operatorTable = new TripOperatorsTable($this->_adapter);
        $tripItemTable = new TripItemsTable($this->_adapter);
        $saleChargeTable = new SaleChargesTable($this->_adapter);
        $currencyTable = new TripCurrencyTable($this->_adapter);
        $tripContent = new TripContentsTable($this->_adapter);
        $listStatus = $bookingTable->getListStatus();
        /** @var BookingItemsExtrasTable $bookingExtras */
        $bookingExtras = Tls::get('BookingItemsExtrasTable');

        $booking = $bookingTable->getItem($refno);
        $traveller = $booking ? $bookingTravel->getItem($booking->TravellerID) : false;
        $operator = $booking ? $operatorTable->getTourSys($booking->OperatorID) : false;
        $bookingItems = $bookingItem->getItems($refno);
        $paymens = $bookingPayment->getItemRefNo($refno);
        $tripItem = $bookingItems ? $tripItemTable->getItem(current($bookingItems)['TourID']) : false;
        $bkItem = $bookingItems ? current($bookingItems) : '';
        $bkItemsExtras = $bookingExtras->getItems($refno);

        $booking->OperatorName = $operator ? $operator->tour_name : '';
        $booking->TourID = !empty($bkItem) ? $bkItem['TourID'] : '';
        $booking->TourName = !empty($bkItem) ? $bkItem['TourName'] : '';
        $booking->ContactInfo = $operator ? $operator->contact_info : '';
        $booking->Location = $tripItem ? $tripItem->TourLocation : '';
        $booking->Duration = !empty($bkItem) ? $bkItem['TourDuration'] : ($tripItem ? $tripItem->TourDuration : '');
        $booking->DurationType = !empty($bkItem) ? $bkItem['TourDurationType'] : ($tripItem ? $tripItem->TourDurationType : '');
        $booking->DepatureTime = !empty($bkItem) ? $bkItem['DepartureTime'] : '';
        $booking->BkStatus = $listStatus[$booking->BkStatus];
        $booking->Symbol = $currencyTable->getSymbol($booking->SaleCurrency);
        $booking->Items = $bkItem;
        // Booking Items Extras
        $dataExtras = array();
        if($bkItemsExtras){
            foreach($bkItemsExtras as $extra){
                $extraName = $extra['ExtraName'];
                if($lang != TLS_LANG_DEFAULT){
                    /** @var ExtrasItemsTable $ExtraTable */
                    $ExtraTable = Tls::get('ExtrasItemsTable');
                    /** @var ExtrasItemsEntity $ExtrasEntity */
                    $ExtrasEntity = $ExtraTable->getItem($extra['ExtraID']);
                    if($ExtrasEntity){
                        $Title = !empty($ExtrasEntity->Title) ? json_decode($ExtrasEntity->Title, true) : '';
                        if(!empty($Title[$lang])){
                            $extraName = $Title[$lang];
                        }
                    }
                    $extra['ExtraName'] = $extraName;
                }
                $dataExtras[] = $extra;
            }
            $booking->Extras = $dataExtras;
        }

        $this->_booking = $booking ? $booking : false;
        $this->_traveller = $traveller ? $traveller : false;
        $this->_payment = $paymens ? $paymens : array();
        $this->_info = array('tour' => $tripItem, 'content' => $tripContent->getItem($booking->TourID, $lang));

        // Class
        $class = new \stdClass();
        $class->booking = $bookingTable;
        $class->traveller = $bookingTravel;
        $class->payment = $bookingPayment;
        $class->operator = $operatorTable;
        $class->saleCharge = $saleChargeTable;
        $class->currency = $currencyTable;
        $this->_class = $class;
    }

    /**
     * @param bool|false $isDownload
     */
    public function createVoucher($isDownload = false , $lang = 'en') {
        return module_invoke('bkngin', 'booking_view_voucher', $this, $isDownload, $lang);
    }

    /**
     * @param $refno
     * @param array $options
     */
    public function sendMail($refno, array $options) {
        global $conf, $user;
        module_load_include('inc', 'tls', 'tls.common');
        module_load_include('inc', 'tls', 'tls.library');
        module_load_include('inc', 'bkngin', 'bkngin.common');

        $lang = isset($options['lang']) ? $options['lang'] : 'en';
        $this->loadData($refno, $lang);
        // Confirm Traveller
        //list($subject, $content, $receive_name, $receive_email) = $this->proccessConfirmToTraveler($lang);
        list($subject, $content, $receive_name, $receive_email) = $this->proccessContentEmail($lang, $options);
        $attachment = null;
        if ( !empty($options['attachment']) && $options['attachment'] ) {
            list($filePath, $fileName) = $this->createVoucher(false, $lang);
            if ( file_exists($filePath) ) {
                $attachment['src']  = 'source';
                $attachment['data'] = file_get_contents($filePath);
                $attachment['name'] = $fileName;
                $attachment['type'] = 'application/pdf';
            }
        }

        $contact = unserialize($this->_booking->ContactInfo);
        $bcc = null;
        if ( !empty($options['BCC']) && $options['BCC'] ) {
        	$bcc = array(array(
			    'email' => trim($contact->reservation_email),
			    'name' => $this->_booking->OperatorName
        	));
        }

        $reply_to = null;
        if ( !empty($options['reply-to']) && $options['reply-to'] ) {
        	$reply_to = trim($contact->reservation_email);
        }

        $receivers = tls_parse_email_list($receive_email, $receive_name);
        module_invoke('tls', 'send_mail', 'no-reply@connectours.org', $conf['tls']['support_email'],
            $receivers, null, $bcc, $subject, $content, 'Html', array($attachment), $reply_to
        );
    }

    /**
     * @param $payment
     * @param $booking
     * @param $lang
     * @param $content
     * @param $result
     */
    protected function getPayment($payment, $booking, $lang, $content, &$result) {
        $resources = self::getResouces($lang);

        $classPayment = $this->_class->payment;
        $opSubject = '.';
        $processCCMessage = '';
        $pmText = '';
        if ( count($payment) > 0 ) {
            foreach ( $payment as $pay ) {
                $pmCode = $pay['PmType'];
                // Case CC
                if ( $pay['PmType'] == TLS_PM_METHOD_CC ) {
                    $pmCode = !empty($pay['CCType']) ? ucfirst($pay['CCType']) : TLS_PM_METHOD_CC;
                    $strComment = Util::partitionString('<!--s-bank-message-note-->', '<!--e-bank-message-note-->', $content);
                    $content = $strComment[0].$strComment[2];
                    $content = tls_remove_string('<!--s-pp-other-message-note-->', '<!--e-pp-other-message-note-->', $content);

                    if ( floatval($booking->TotalDeposit) > 0
                        && $pay['PmStatus'] == $classPayment::PS_PENDING
                    ) {
                        $processCCMessage = '<li style="line-height:20px; padding:0; margin:0;">';
                        //$processCCMessage .= t('Your credit card payment is in process. We will contact you in case of any problem.', array(), array('context' => 'tls;system:1;module:5;section:9', 'langcode' => $lang));
                        $processCCMessage .= $resources['CARD_INPROCESS'];
                        $processCCMessage .='</li>';
                    }

                    if ( $pay['PmStatus'] == $classPayment::PS_RECEIVED ) {
                        //$pmText = t('Paid via', array(), array('context' => 'tls;system:1;module:5;section:9', 'langcode' => $lang));
                        $pmText = $resources['PAID_VIA'];
                    }
                    else {
                        //$pmText = t('To be processed via', array(), array('context' => 'tls;system:1;module:5;section:9', 'langcode' => $lang));
                        $pmText = $resources['PROCESS_VIA'];
                    }
                }

                $arrival = $booking->TotalCost - $pay['ReceivedAmt'];
                //$content = str_replace('{{booking.deposit}}', Util::formatNumber($pay['ReceivedAmt'], 1, 2), $content);
                //$content = str_replace('{{booking.received}}', Util::formatNumber($arrival, 1, 2), $content);
                //$content = str_replace('{{booking.balance_due_arrival}}', t('Balance due on arrival', array(), array('context' => 'tls;system:1;module:5;section:9', 'langcode' => $lang)), $content);
                $content = str_replace('{{booking.balance_due_arrival}}', $resources['BALANCE_ON_ARRIVAL'], $content);

                // Case Bank Transfer
                if ($pay['PmType'] == TLS_PM_METHOD_BANK) {
                	$strComment = Util::partitionString('<!--s-cc-message-note-->', '<!--e-cc-message-note-->', $content);
                	$content = $strComment[0].$strComment[2];
                	$content = tls_remove_string('<!--s-pp-other-message-note-->', '<!--e-pp-other-message-note-->', $content);
                    //$opSubject = ', ' . t('but pending payment');
                	$opSubject = ', ' . $resources['BUT_PENDING_PMT'];
                    //$pmName = t('Bank Transfer', array(), array('context' => 'tls;system:1;module:5;section:9', 'langcode' => $lang));
                    $pmName = $resources['BANK_TRANSFER'];
                    //$pmText = t('Pending deposit via');
                    $pmText = $resources['PENDING_DEPOSIT_VIA'];
                    $content = str_replace('{{booking.bank}}', $pmName, $content);

                    //Replace bank_limit
                    $merchantSetting = new MerchantSettingTable($this->_adapter);
                    $paramDeadline = $merchantSetting->getParams(TLS_PM_METHOD_BANK,'deadline');
                    $paramDeadlineId = $paramDeadline->ParamId;
                    $deadlineDefault = $paramDeadline->Value;
                    $deadline = '';

                    $paramAccount = $merchantSetting->getParams(TLS_PM_METHOD_BANK, 'account');
                    $paramAccountId = $paramAccount->ParamId;
                    $accountDefault = $paramAccount->Value;
                    $account = '';
                    $operator = $this->_class->operator->getItem($this->_booking->OperatorID);
                    $operatorPayments = json_decode($operator->Payments);
                    if ( isset($operatorPayments->Bank) ) {
                        foreach ( $operatorPayments->Bank as $paramBank ) {
                            if ( $paramBank->ParamId == $paramDeadlineId ) {
                                $deadline = implode('', $paramBank->Value);
                            }

                            if ( $paramBank->ParamId == $paramAccountId ) {
                                $account = implode('', $paramBank->Value);
                            }
                        }

                        $content = str_replace("{{booking.bank_limit}}", $deadline , $content);
                        $content = str_replace("{{booking.bank.account_details}}", $account , $content);
                    }
                    else {
                        $content = str_replace("{{booking.bank_limit}}", $deadlineDefault , $content);
                        $content = str_replace("{{booking.bank.account_details}}", $accountDefault , $content);
                    }
                }

                if ( $pay['PmType'] == TLS_PM_METHOD_NONE ) {
                	$content = tls_remove_string('<!--s-paid-via-->', '<!--e-paid-via-->', $content);
                	$content = tls_remove_string('<!--s-cc-message-note-->', '<!--e-cc-message-note-->', $content);
                	$content = tls_remove_string('<!--s-bank-message-note-->', '<!--e-bank-message-note-->', $content);
                	$content = tls_remove_string('<!--s-pp-other-message-note-->', '<!--e-pp-other-message-note-->', $content);
                }

                $content = str_replace("{{booking.payment_label}}", $pmCode , $content);
                $content = str_replace("{{booking.process_credit_card_message}}", $processCCMessage , $content);
                $content = str_replace('{{booking.payment_text}}', $pmText . ' ' . $pmCode, $content);
                $content = str_replace('{{booking.option_subject}}', $opSubject, $content);
            }
        }
        else {
        	$content = str_replace('{{booking.option_subject}}', '', $content);
        	$content = str_replace("{{booking.process_credit_card_message}}", '' , $content);
        	$content = tls_remove_string('<!--s-paid-via-->', '<!--e-paid-via-->', $content);
        	$content = tls_remove_string('<!--s-cc-message-note-->', '<!--e-cc-message-note-->', $content);
        	$content = tls_remove_string('<!--s-bank-message-note-->', '<!--e-bank-message-note-->', $content);
        	$content = tls_remove_string('<!--s-pp-other-message-note-->', '<!--e-pp-other-message-note-->', $content);
        }

        $result = $content;
    }

    /**
     * @param $lang
     * @return array
     */
    protected function buildDefault($lang) {
        global $conf;
        module_load_include('inc', 'tls', 'tls.common');
        module_load_include('inc', 'bkngin', 'bkngin.constant');
        /** @var BookingsTable $BookingsTable */
        $BookingsTable = $this->_class->booking;
        $listChannel = $BookingsTable->getListChannel();

        $resources = self::getResouces($lang);

        $contact = unserialize($this->_booking->ContactInfo);
        $number_pax = '';
        $number_pax .= $this->_booking->AdultNum;
        $number_pax .= ' ';
        $number_pax .= $this->_booking->AdultNum > 1 ? $resources['ADULTS'] : $resources['ADULT'];
        if ( $this->_booking->ChildNum > 0 ) {
            $number_pax .= ', ';
            $number_pax .= $this->_booking->ChildNum;
            $number_pax .= ' ';
            $number_pax .= $this->_booking->ChildNum > 1 ? $resources['CHILDREN'] : $resources['CHILD'];
        }

        $duration = $this->_booking->Duration;
        $duration .= ' ';
        if ( $this->_booking->DurationType == TYPE_DAY ) {
            $duration .= $this->_booking->Duration > 1 ? $resources['DAYS'] : $resources['DAY'];
        }
        else {
            $duration .= $this->_booking->Duration > 1 ? $resources['HOURS'] : $resources['HOUR'];
        }

        $view_booking_url = "{$conf['tls']['domain']['manage']}/#/booking-engine/booking-management/booking-details/{$this->_booking->OperatorID}/{$this->_booking->ExtRefNo}";
        $list_replace = array(
            '{{booking.source}}' => $listChannel[$this->_booking->SaleChannel],
            '{{booking.tour.operator}}' => $this->_booking->OperatorName,
            '{{booking.tour.operator.email}}' => $contact->reservation_email,
            '{{booking.tour.operator.phone}}' => $contact->phone,
            //'{{booking.contact.tour.operator.or}}' => ' ' . t('or', array(), array('context' => 'tls;system:1;module:5;section:9', 'langcode' => $lang)) .' ',
            '{{booking.contact.tour.operator.or}}' => ' ' . $resources['OR'] . ' ',
            '{{booking.id}}' => $this->_booking->ExtRefNo,
            '{{booking.tour_name}}' => $this->_booking->TourName,
            '{{booking.url_view_booking}}' => $view_booking_url,
            '{{booking.guest.f_name}}' => $this->_traveller->FirstName,
            '{{booking.guest.l_name}}' => $this->_traveller->LastName,
            '{{booking.guest.email}}' => trim($this->_traveller->Email),
            '{{booking.guest.phone}}' => $this->_traveller->Phone,
            '{{booking.number.pax}}' => $number_pax,
            '{{booking.item_total_price}}' => Util::formatNumber($this->_booking->Items['Amount'], 1, 2),
            '{{booking.duration}}' => $duration,
            '{{booking.pickup.time}}' => date('d M Y', $this->_booking->StartDate) . '  (' . tls_time_to_string($this->_booking->DepatureTime) . ')',
            //'{{booking.pickup.time}}' => date('d M Y', $this->_booking->StartDate),
            '{{booking.pickup.location}}' => $this->_booking->HotelLocation,
        	'{{booking.status_text}}' => $this->_booking->BkStatus,
            '{{booking.note.comment}}' => $this->_booking->BkRequest,
            '{{booking.symbol}}' => $this->_booking->Symbol,
            '{{booking.grand_total}}' => Util::formatNumber($this->_booking->TotalAmount + $this->_booking->PaymentFee , 1, 2),
            '{{booking.deposit}}' => Util::formatNumber($this->_booking->TotalDeposit + $this->_booking->PaymentFee, 1, 2),
        	'{{booking.received}}' => Util::formatNumber($this->_booking->TotalAmount - $this->_booking->TotalDeposit, 1, 2),
            //'{{booking.balance_due_arrival}}' => t('Balance due on arrival', array(), array('context' => 'tls;system:1;module:5;section:9', 'langcode' => $lang)),
            '{{booking.balance_due_arrival}}' => $resources['BALANCE_ON_ARRIVAL'],
            '{{booking.link_tls}}' => $conf['tls']['domain']['manage'],
            '{{booking.date}}' => date('d-M-Y', $this->_booking->BkDate)
        );

        return $list_replace;
    }

    /**
     * @param $content
     */
    protected function removeString($content) {
        $list_key = array(
            '[PICKUP-TIME]', '[/PICKUP-TIME]',
            '[DEPARTURE-TIME]', '[/DEPARTURE-TIME]',
            '[PICKUP-LOCATION]', '[/PICKUP-LOCATION]',
            '[MEETING-POINT]', '[/MEETING-POINT]'
        );
        if ( (int)$this->_info['tour']->IsMeetingPoint == 1 ) {
            // Remove
            $tmpPickup = Util::partitionString('[PICKUP-TIME]', '[/PICKUP-TIME]', $content);
            $content = $tmpPickup[0].$tmpPickup[2];
            $tmpLocation = Util::partitionString('[PICKUP-LOCATION]', '[/PICKUP-LOCATION]', $content);
            $content = $tmpLocation[0].$tmpLocation[2];
        }
        else {
            // Remove
            $tmp = Util::partitionString('[DEPARTURE-TIME]', '[/DEPARTURE-TIME]', $content);
            $content = $tmp[0] . $tmp[2];
            $tmpLocation = Util::partitionString('[MEETING-POINT]', '[/MEETING-POINT]', $content);
            $content = $tmpLocation[0] . $tmpLocation[2];
        }

        $content = str_replace($list_key, array('', '', '', '', '', '', '', ''), $content);

        // Remove payment other if payment not use
        if(!empty($this->_payment)){
            $bank = $cc = false;
            $payments = $this->_payment;
            foreach($payments as $pm){
                if($pm['PmType'] == TLS_PM_METHOD_BANK){
                    $bank = true;
                }
                if($pm['PmType'] == TLS_PM_METHOD_CC){
                    $cc = true;
                }
            }
            if(!$bank){
                $content = tls_remove_string('<!--s-bank-message-note-->', '<!--e-bank-message-note-->', $content);
            }
            if(!$cc){
                $content = tls_remove_string('<!--s-cc-message-note-->', '<!--e-cc-message-note-->', $content);
            }
        }

        // Remove Extras if Not Booking Extras
        if(!isset($this->_booking->Extras)){
            $tmp = Util::partitionString('<!--s-booking-extras-->' , '<!--e-booking-extras-->', $content);
            $content = $tmp[0] . $tmp[2];
        }
        // Remove Payment fee
        if(empty($this->_booking->PaymentFee) || floatval($this->_booking->PaymentFee) <= 0){
            $tmp = Util::partitionString('<!--s-payment-surcharge-->' , '<!--e-payment-surcharge-->', $content);
            $content = $tmp[0] . $tmp[2];
        }

        return $content;
    }

    /**
     * @param $booking
     * @param $lang
     * @param $content
     * @param $result
     */
    protected function getSaleCharge($booking, $lang, $content, &$result) {
        $resources = self::getResouces($lang);

        // Taxes and Additional Fees
        $saleChargeTable = $this->_class->saleCharge;
        $saleCharges = $saleChargeTable->getItemsByOpID($booking->OperatorID);
        $symbol= $booking->Symbol;
        $strSalecharge = '';
        if ( $saleCharges ) {
            //$strSalecharge = t('Prices include', array(), array('context' => 'tls;system:1;module:5;section:9', 'langcode' => $lang));
            $strSalecharge = $resources['PRICE_INCLUDE'];
            for ( $i = 0; $i < count($saleCharges); $i++ ) {
                if ( $i > 0 ) {
                    $strSalecharge .= ', ';
                }

                if ( (int)$saleCharges[$i]['Type'] == 2 ) {
                    $strSalecharge .= ' '.$saleCharges[$i]['Amount'] . '%';
                }
                else {
                    $strSalecharge .= ' ' . $symbol.' ';
                    $strSalecharge .= $saleCharges[$i]['Amount'] . ' ';
                    if ( (int)$saleCharges[$i]['Unit'] == 2 ) {
                        //$strSalecharge .= t('per tour', array(), array('context' => 'tls;system:1;module:5;section:9', 'langcode' => $lang));
                        $strSalecharge .= $resources['PER_TOUR'];
                    }
                    else {
                        //$strSalecharge .= t('per pax', array(), array('context' => 'tls;system:1;module:5;section:9', 'langcode' => $lang));
                        $strSalecharge .= $resources['PER_PAX'];
                    }
                }

                $strSalecharge .= ' ' . $saleCharges[$i]['Name'];
            }

            $content = str_replace('{{booking.payment_note}}', $strSalecharge, $content);
        }
        else {
            $content = str_replace('{{booking.payment_note}}', '', $content);
        }

        $result = $content;
    }

    /**
     * @param string $lang
     * @return array
     */
    public function proccessConfirmToTraveler($lang = 'en') {
        $booking = $this->_booking;
        $traveller = $this->_traveller;
        $payment = $this->_payment;

        $bookingTable = $this->_class->booking;
        $corr = $bookingTable->getCorrespondences(TLS_CORR_BK_CONFIRM_TO_TRAVELLER, $lang);

        /* process subject */
        $subject = $corr['corr_subject'];
        $subject = str_replace('{{booking.id}}', $booking->RefNo, $subject);
        $subject = str_replace('{{booking.source}}', $booking->OperatorName, $subject);

        /* process content */
        $strReplace = $this->buildDefault($lang);
        $content = $corr['corr_content'];
        $content = str_replace(array_keys($strReplace), array_values($strReplace), $content);
        $this->getPayment($payment, $booking, $lang, $content, $pm);
        $content = $pm;

        // Taxes and Additional Fees
        $this->getSaleCharge($booking, $lang, $content, $sale);
        $content = $sale;

        $policy = module_invoke('bkngin', 'build_booking_policy', $booking->TourID);
        $content = str_replace('{{booking.policies}}', $policy, $content);

        $content = $this->removeString($content);

        $receive_name = $traveller->FirstName . ' ' . $traveller->LastName;
        $receive_email = trim($traveller->Email);

        return array($subject, $content, $receive_name, $receive_email);
    }

    /**
     * @param string $lang
     * @return array
     */
    public function proccessContentEmail($lang = 'en', array $options) {
    	$booking = $this->_booking;
    	$traveller = $this->_traveller;
    	$payment = $this->_payment;

    	$bookingTable = $this->_class->booking;
    	$contact = unserialize($this->_booking->ContactInfo);
        /** @var BookingsTable $bookingTable */
    	$corr = $bookingTable->getCorrespondences($options['CorrId'], $lang);

    	/* process subject */
    	$subject = $corr['corr_subject'];
    	$subject = str_replace('{{booking.id}}', $booking->ExtRefNo, $subject);
    	$subject = str_replace('{{booking.source}}', $booking->OperatorName, $subject);
    	$subject = str_replace('{{booking.tour_name}}', $booking->TourName, $subject);

    	/* process content */
    	$strReplace = $this->buildDefault($lang);
    	$content = $corr['corr_content'];
    	$content = str_replace(array_keys($strReplace), array_values($strReplace), $content);
    	if ( $booking->BkRequest == '' ) {
    		$content = tls_remove_string('<!--s-note-comment-->', '<!--e-note-comment-->', $content);
    	}

    	$this->getPayment($payment, $booking, $lang, $content, $pm);
    	$content = $pm;

        // Booking Items Extras
        $content = $this->processBookingExtras($content, $lang, $options);

    	// Taxes and Additional Fees
    	$this->getSaleCharge($booking, $lang, $content, $sale);
    	$content = $sale;

    	//$policy = module_invoke('bkngin', 'build_booking_policy', $booking->TourID);
    	$policy = $this->getBookingPolicies($booking->TourID, $booking->OperatorID, $lang);
        $policyExtras = $this->getBookingExtrasPolices($booking, $lang, 0);
        if(!empty($policyExtras)){
            $policy .= $policyExtras;
        }
    	$content = str_replace('{{booking.policies}}', $policy, $content);

    	$content = $this->removeString($content);

        $receive_name = '';
        $receive_email = '';

    	if(isset($options['Traveller']) && $options['Traveller']) {
    		$receive_name = $traveller->FirstName . ' ' . $traveller->LastName;
    		$receive_email = trim($traveller->Email);
            if(!empty($this->_booking->PaymentFee) && floatval($this->_booking->PaymentFee) > 0){
                $content = str_replace('{{booking.payment_surcharge}}', Util::formatNumber($this->_booking->PaymentFee, 1, 2), $content);
            }
    	}

    	if ( isset($options['Operator']) && $options['Operator'] ) {
    		$receive_name = $booking->OperatorName;
    		$receive_email = trim($contact->reservation_email);
    	}

    	return array($subject, $content, $receive_name, $receive_email);
    }

    /**
     * @param $lang
     * @return mixed
     */
    public function processConfirmVoucher($lang = 'en') {
        $resources = self::getResouces($lang);

        $booking = $this->_booking;
        $traveller = $this->_traveller;
        $payment = $this->_payment;

        $bookingTable = $this->_class->booking;
        $corr = $bookingTable->getCorrespondences(TLS_CORR_BK_VOUCHER, $lang);
        $symbol= $booking->Symbol;

        /* process content */
        $content = $corr['corr_content'];
        $strReplace = $this->buildDefault($lang);
        $content = str_replace(array_keys($strReplace), array_values($strReplace), $content);
        $opSubject = '';
        if ( count($payment) > 0 ) {
        	foreach ( $payment as $pay ) {
        		if ( $pay['PmType'] == TLS_PM_METHOD_BANK ) {
        			//$opSubject = t('pending payment');
        		    $opSubject = $resources['PENDING_PMT'];
        		}
        	}
        }

        $content = str_replace('{{booking.option_subject}}', $opSubject, $content);
        if ( $booking->BkRequest == '' ) {
        	$content = tls_remove_string('<!--s-note-comment-->', '<!--e-note-comment-->', $content);
        }

        $this->getPayment($payment, $booking, $lang, $content, $pm);
        $content = $pm;

        // Booking Items Extras
        $content = $this->processBookingExtras($content, $lang, array('Operator'=>true));

        $contact = unserialize($this->_booking->ContactInfo);
        $cityName = $this->_class->operator->getCityName($booking->OperatorID);
        $countryName = $this->_class->operator->getCountryName($booking->OperatorID);
        $content = str_replace('{{booking.tour.operator.address}}', $contact->address, $content);
        if ( !empty($contact->state) ) {
            $content = str_replace('{{booking.tour.operator.city.state}}', $contact->state . ', ' . $cityName->name, $content);
        }
        else {
            $content = str_replace('{{booking.tour.operator.city.state}}', $cityName->name, $content);
        }

        $content = str_replace('{{booking.tour.operator.country}}', $countryName->name, $content);
        $content = str_replace('{{booking.tour.operator.phone.voucher}}', $contact->phone, $content);

        if ( !empty($contact->fax) && $contact->second_phone_title == 'Mobile' ) {
            $content = str_replace('{{booking.tour.operator.mobile}}', $contact->fax, $content);
        }
        else {
            $content = str_replace('{{booking.tour.operator.mobile}}', '', $content);
            $content = tls_remove_string('<!--s-voucher-mobile-->', '<!--e-voucher-mobile-->', $content);
        }

        if ( !empty($contact->fax) && $contact->second_phone_title == 'Fax' ) {
            $content = str_replace('{{booking.tour.operator.fax}}', $contact->fax, $content);
        }
        else {
        	$content = str_replace('{{booking.tour.operator.fax}}', '', $content);
        	$content = tls_remove_string('<!--s-voucher-fax-->', '<!--e-voucher-fax-->', $content);
        }

        $content = str_replace('{{booking.tour.operator.email.voucher}}', $contact->reservation_email, $content);

        // Taxes and Additional Fees
        $this->getSaleCharge($booking, $lang, $content, $sale);
        $content = $sale;

        //$policy = module_invoke('bkngin', 'build_booking_policy', $booking->TourID);
        $policy = $this->getBookingPolicies($booking->TourID, $booking->OperatorID, $lang);
        $policyExtras = $this->getBookingExtrasPolices($booking, $lang, 1);
        if(!empty($policyExtras)){
            $policy .= '<br>' . $policyExtras;
        }
        $content = str_replace('{{booking.policies}}', $policy, $content);

        $content = $this->removeString($content);

        return $content;
    }

    /**
     * @param $content
     * @param $lang
     * @return string
     * @throws \Exception
     */
    protected function processBookingExtras($content, $lang, $options){
        try{
            if(!empty($this->_booking->Extras)){
                $arrThis = array('{{booking.extras.extra_name}}');
                if(isset($options['Operator']) && $options['Operator']){
                    $arrThis[] = '{{booking.extras.guest_details}}';
                }
                if(isset($options['Traveller']) && $options['Traveller']){
                    $arrThis[] = '{{booking.extras.number_of_pax}}';
                    $arrThis[] = '{{booking.extras.pax_detail}}';
                }
                $bookingExtras = $this->_booking->Extras;
                $tmpExtras = Util::partitionString('<!--s-list-booking-extras-->', '<!--e-list-booking-extras-->', $content);
                $strExtras = '';
                foreach($bookingExtras as $item){
                    $strTmp = $tmpExtras[1];
                    $arrBy = array($item['ExtraName']);
                    $builTextPax = $this->processTextPax($item, $lang);
                    if(isset($options['Operator']) && $options['Operator']){
                        $arrBy[] = $builTextPax;
                        if(!empty($strExtras))
                            $strExtras .= ', ';
                    }
                    if(isset($options['Traveller']) && $options['Traveller']){
                        $arrBy[] = $item['AdultNum'] + $item['ChildNum'];
                        $arrBy[] = $builTextPax;
                        $strTmp = str_replace('{{booking.extras.total_price}}', Util::formatNumber($item['Amount'], 1, 2), $strTmp);
                    }

                    $strTmp = str_replace($arrThis, $arrBy, $strTmp);
                    $strExtras .= $strTmp;
                }
                $content = $tmpExtras[0] . $strExtras . $tmpExtras[2];
            }
            return $content;
        }catch (\Exception $ex){
            throw $ex;
        }
    }

    /**
     * @param $item
     * @return string
     */
    protected function processTextPax($item, $lang){
        $resources = self::getResouces($lang);
        $Travellers = '';
        // Adults
        if((int)$item['AdultNum'] > 1)
            $Travellers .= $item['AdultNum']. ' '. $resources['ADULTS'];
        else
            $Travellers .= $item['AdultNum']. ' '. $resources['ADULT'];
        // Childs
        if(!empty($item['ChildNum']) && intval($item['ChildNum']) > 0){
            $Travellers .= ' '.$resources['AND']. ' ';
            if((int)$item['ChildNum'] > 1)
                $Travellers .= $item['ChildNum'].' '. $resources['CHILDREN'];
            else
                $Travellers .= $item['ChildNum'].' '. $resources['CHILD'];
        }
        return $Travellers;
    }

    /**
     * @param $booking
     * @param $lang
     * @return string
     * @throws Exception
     * @throws \Exception
     */
    protected function getBookingExtrasPolices($booking, $lang, $type){
        // Policy booking extras if has, $type = 1 : voucher, $type = 0 : email
        $policyExtras = '';
        if(isset($this->_booking->Extras)){
            $bookingExtras = $this->_booking->Extras;
            foreach($bookingExtras as $extra){
                $pEx = $this->getBookingPolicies($extra['ExtraID'], $booking->OperatorID, $lang, 1);
                $extraName = '<strong style="font-size: 13px;">'.$extra['ExtraName'].'</strong><br>';
                if ( !empty($policyExtras) && $type == 1 ){
                    $policyExtras .= '<br><p>'.$extraName.$pEx.'</p>';
                }else{
                    $policyExtras .= '<p>'.$extraName.$pEx.'</p>';
                }
            }
        }
        return $policyExtras;
    }
    /**
     * Get Full Policies
     */
    protected function getBookingPolicies($tour_id, $operator_id, $lang = 'en', $type = null) {
        $resources = self::getResouces($lang);

    	/* @var $bkconTable BkconditionsTable */
    	$bkconTable = Tls::get('BkconditionsTable');
    	//$Messages = $GLOBALS['BE_MESSAGE'];
    	$full_policies = '';

    	try {
            // Type =1 : Extra
            if(!empty($type) && $type == 1){
                $bkcon = $bkconTable->getItem($tour_id, $operator_id, 1);
            }else {
                $bkcon = $bkconTable->getItem($tour_id, $operator_id);
            }
    		if ( !$bkcon ) {
    			$bkcon = $bkconTable->getItem(0, $operator_id);
    		}
            $tourName = '<strong style="font-size: 13px;">'.$this->_booking->TourName.'</strong><br>';
    		if ( $bkcon ) {
    			$depositType = '';
    			switch ( $bkcon->DepositType ) {
    				case Tls::DEPOSIT_NO :
    					$depositType = $resources['NO_DEPOSIT'];
    					break;

    				case Tls::DEPOSIT_FIXED_AMOUNT :
    					$depositType = $resources['FIXED_DEPOSIT'];
    					$depositType = str_replace('@fixed_amount', 'USD ' . $bkcon->DepositAmt, $depositType);
    					break;

    				case Tls::DEPOSIT_PERCENTAGE :
    					$depositType = $resources['PERCENT_DEPOSIT'];
    					$depositType = str_replace('@percent_amount', $bkcon->DepositAmt . '%', $depositType);
    					break;

    				default:
    					$depositType = $resources['FULL_DEPOSIT'];
    			}

    			//$deposit = '<strong>' . t('Payment:') . ' </strong>' . $depositType . '<br/>';
    			$deposit = "<strong>{$resources['PAYMENT']}:</strong> {$depositType}<br/>";
    			$cancel = $this->getCancelationRules(json_decode($bkcon->CancellationRules), $resources);
    			//$cancelation = '<strong>' . t('Cancellation:') . ' </strong>' . $cancel . '<br/>';
    			$cancelation = "<strong>{$resources['CANCELLATION']}:</strong> {$cancel}<br/>";

    			module_load_include('inc', 'tls', 'tls.common');
    			$other = module_invoke('tls', 'get_lang_text', $bkcon->OtherPolicies, $lang);
    			//$otherPolicies = '<strong>' . t('Additional note:') . ' </strong>' . $other . '<br/>';
    			$otherPolicies = !empty($other) ? "<strong>{$resources['ADDITIONAL_NOTE']}:</strong> {$other}<br/>" : '';
    			$full_policies = !empty($type) && $type ? $cancelation . $deposit . $otherPolicies : '<p>' . $tourName . $cancelation . $deposit . $otherPolicies . '</p>';
    		}

    		return $full_policies;
    	}
    	catch ( Exception $ex ) {
    		throw $ex;
    	}
    }

    /**
     * Get Cancelation Rules
     */
    protected function getCancelationRules($conditions = array(), $messages) {
    	if ( empty($conditions) || count($conditions) < 2 ) {
    		return '';
    	}

    	try {
    		$textBasic = $text = '';
    		// No other policy except the No-show and 0 day
    		$isOnlyBasic = count($conditions) == 2;
    		// No-show and 0 day have the same penalty
    		$noShow = $conditions[0]->Day == 'No-show' ? $conditions[0] : false;
    		$zeroDay = $conditions[1]->Day == '0' ? $conditions[1] : false;
    		$isSamePenalty = ($noShow && $zeroDay && $noShow->Type == $zeroDay->Type)
    		    ? ($zeroDay->Type == Tls::CANCELLATION_PERCENTAGE || $zeroDay->Type == Tls::CANCELLATION_FIXED_AMOUNT
    		        ? $noShow->Amount == $zeroDay->Amount : true)
    		    : false;

    		if ( $isOnlyBasic && $isSamePenalty )	{ // only the No-show & 0 day and same penalty
    			$textBasic = $messages['BK_CONDITION_0'];
    		}
    		else if ( $isOnlyBasic )	{ // only the No-show & 0 day and different penalty
    			$textBasic = $messages['BK_CONDITION_1'];
    			$amount_type_1 = $this->getAmountType($zeroDay, $messages);
    			$amount_type_2 = $this->getAmountType($noShow, $messages);
    			$textBasic = str_replace('@amount_type_1', $amount_type_1, $textBasic);
    			$textBasic = str_replace('@amount_type_2', $amount_type_2, $textBasic);
    		}
    		else if ( !$isOnlyBasic && $isSamePenalty ) {	// more than the No-show & 0 day and same penalty
    			$textBasic = $messages['BK_CONDITION_2'];
    			$amount_type_1 = $this->getAmountType($noShow, $messages);
    			$textBasic = str_replace('@amount_type', $amount_type_1, $textBasic);
    		}
    		else {	// more than the No-show & 0 day and different penalty
    			$textBasic = $messages['BK_CONDITION_3'];
    			$amount_type_1 = $this->getAmountType($zeroDay, $messages);
    			$amount_type_2 = $this->getAmountType($noShow, $messages);
    			$textBasic = str_replace('@amount_type_1', $amount_type_1, $textBasic);
    			$textBasic = str_replace('@amount_type_2', $amount_type_2, $textBasic);
    		}

    		// Loop all rules
    		$first = true;
    		$popup_cancelation = '';
    		$preObjCancel = null;
    		for ( $i = count($conditions) - 1; $i >= 0; $i -- ) {
    			$cancel = $conditions[$i];
    			// Only get text except No-show & 0 day
    			if ( !isset($cancel->Type) || !isset($cancel->Day)
					|| $cancel->Day == '0' || $cancel->Day == 'No-show'
    			) {
    				continue;
    			}

    			$text .= strlen($text) == 0 ? '' : ' ';
    			if ( $first ) {
    				$tmp = $messages['BK_CONDITION_5'];
    				$amount_type = $this->getAmountType($cancel, $messages);
    				$tmp = str_replace('@amount_type', $amount_type, $tmp);
    				$tmp = str_replace('@num', $cancel->Day, $tmp);
    				$tmp = str_replace('@day', $cancel->Day > 1 ? $messages['DAYS'] : $messages['DAY'], $tmp);
    				$text .= $tmp;
    			}
    			else {
    				$tmp = $messages['BK_CONDITION_4'];
    				$amount_type = $this->getAmountType($cancel, $messages);
    				$tmp = str_replace('@amount_type', $amount_type, $tmp);
    				$tmp = str_replace('@from', $cancel->Day, $tmp);
    				$tmp = str_replace('@to', $preObjCancel->Day, $tmp);
    				$tmp = str_replace('@day', $preObjCancel->Day > 1 ? $messages['DAYS'] : $messages['DAY'], $tmp);
    				$text .= $tmp;
    			}

    			$preObjCancel = $cancel;
    			$first = false;
    		}

    		if ( $preObjCancel ) {
    			$textBasic = str_replace('@num', $preObjCancel->Day, $textBasic);
    			$textBasic = str_replace('@day', $preObjCancel->Day > 1 ? $messages['DAYS'] : $messages['DAY'], $textBasic);
    		}

    		$text .= $textBasic;

    		return $text;

    	} catch ( Exception $ex ) {
    		throw $ex;
    	}
    }

    /**
     *
     */
    protected function getAmountType($condition, $messages) {
    	// Get text
    	$charged = '';
    	if ( $condition->Type == Tls::CANCELLATION_PERCENTAGE ) {
    		$charged = $messages['PERCENT_AMOUNT_TYPE'];
    		$charged = str_replace('@percent', $condition->Amount . '%', $charged);
    	}
    	else if (  $condition->Type == Tls::CANCELLATION_FIXED_AMOUNT  ) {
    		//charged = messages.FIXED_AMOUNT_TYPE;
    		$charged = 'USD ' . $condition->Amount;
    	}
    	else if ( $condition->Type == Tls::CANCELLATION_NO ) {
    		$charged = $messages['NO_PENALTY'];
    	}
    	else if ( $condition->Type == Tls::CANCELLATION_FULL_AMOUNT ) {
    		$charged = $messages['FULL_AMOUNT_TYPE'];
    	}

    	return $charged;
    }
}