<?php
namespace tlslib\Merchant\Model;
use Hbe\Common\BaseObject;
use tlslib\db\BookingItemsExtrasTable;
use tlslib\db\BookingsEntity;
use tlslib\Merchant\Model\Resource\Collection\Item;
use tlslib\Tls;
use tlslib\db\BookingItemsTable;
use tlslib\db\BookingTravellersTable;
use tlslib\db\BookingsTable;

class Booking extends \Hbe\Booking\Model\Booking
{
    /**
     * Use for Payment and Booking Manage ($useOther = true)
     *
     * @param string $refNos RefNo1|RefNo2|...
     * @param null $useOther
     */
    public function __construct ($refNos, $useOther = null) {
        if ( empty($useOther) ) {
            $refnoDefault = $refNos;
            $refNos = explode('|', $refNos);
            $arrRefNo = array();
            foreach ( $refNos as $rn ) {
                if ( (int)$rn == 0 ) {
                    $rn = substr($rn, 2);
                }

                $arrRefNo[] = $rn;
            }

            $refNo = reset($arrRefNo);

            $this->setIdName('RefNo');
            $this->setData('RefNo', $refnoDefault);

            /* @var $bookingTable BookingsTable */
            $bookingTable = Tls::get('BookingsTable');
            /* @var $bookingItemsTable BookingItemsTable */
            $bookingItemsTable = Tls::get('BookingItemsTable');
            /* @var $bookingItemsExtraTable BookingItemsExtrasTable */
            $bookingItemsExtraTable = Tls::get('BookingItemsExtrasTable');

            $totalDeposit = 0;
            $totalSurcharge = 0;
            $description = '';
            $extraName  = '';
            $depositExtras = 0;
            $itemsData = $fields = array();
            foreach ( $arrRefNo as $rn ) {
                $bookingInfo = $bookingTable->getItem($rn);
                $this->setData('OperatorID', $bookingInfo->OperatorID);
                if ( $bookingInfo->BkStatus == Booking::BKS_RQ || empty($bookingInfo->TotalDeposit) ) {
                    continue;
                }

                $totalDeposit += (float)$bookingInfo->TotalDeposit;
                $totalSurcharge += (float)$bookingInfo->PaymentFee;
                $description .= $description ? ', ' : '';
                $description .= $rn . ' - ';
                // If has Extras
                $ExtrasItems = $bookingItemsExtraTable->getItems($rn);
                if ( $ExtrasItems ) {
                    foreach ( $ExtrasItems as $extra ) {
                        $depositExtras += (float) $extra['Deposit'];
                        $extraName .= !empty($extraName) ? ', ' . $extra['ExtraName'] : $extra['ExtraName'];
                    }
                }

                $arrItems = $bookingItemsTable->getItems($rn);
                if ( $arrItems ) {
                    foreach ( $arrItems as $item ) {
                        $description .= !empty($extraName) ? $item['TourName'] . ' + (' . $extraName . ')'  : $item['TourName'];
                        // Optimize data item to push Payment
                        $fields['Id'] = $item['BkItemID'];
                        $fields['Deposit'] = (float)$item['Deposit'] + $depositExtras;
                        $fields['Room'] = 1;
                        $fields['HotelName'] = !empty($extraName) ? $item['TourName'] . ' + (' . $extraName . ')'  : $item['TourName'];
                        $fields['PmSurcharge'] = (float)$bookingInfo->PaymentFee;
                        $itemsData[] = $fields;
                        break;
                    }

                    $description .= ' (';
                    $description .= "{pm_currency}" . ' ';
                    $description .= "{pm_item_amount_{$fields['Id']}})";
                }

                $this->setData('PmCurrency', $bookingInfo->LocalCurrency);
            }

            $this->setData('Deposit', $totalDeposit + $totalSurcharge);
            $this->setData('Description', $description);

            // Build Items
            $Items = new Item();
            $Items->setModelItems($itemsData);
            $this->setData('items', $Items);

            // Traveller
            /* @var $bookingTraveller BookingTravellersTable */
            $bookingTraveller = Tls::get('BookingTravellersTable');
            $travellerInfo = $bookingTraveller->getItems($refNo);
            $traveller = new BaseObject();
            $traveller->setFName($travellerInfo->FirstName);
            $traveller->setLName($travellerInfo->LastName);
            $traveller->setEmail($travellerInfo->Email);
            $traveller->setPhone($travellerInfo->Phone);
            $traveller->setAddress($travellerInfo->Address);
            $traveller->setCity($travellerInfo->City);
            $traveller->setPostal($travellerInfo->PostalCode);
            $traveller->setCountry('IDN');
            $this->guest = $traveller;
        }
    }

    public function getDescriptionPaymentGateway() {
    	return $this->getData('Description');
    }
}
