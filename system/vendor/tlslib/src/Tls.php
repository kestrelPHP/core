<?php
namespace tlslib;

final class Tls {
	private static $config = array ();

	/**
	 * Tax Fee Type
	 */
	const TAX_FEE_EMBEDDED = 0;
	const TAX_FEE_CHARGED = 1;
	const TAX_FEE_COLLECTED = 2;

	/**
	 * Deposit Type
	 */
	const DEPOSIT_NO = 0;
	const DEPOSIT_FIXED_AMOUNT = 1;
	const DEPOSIT_PERCENTAGE = 2;
	const DEPOSIT_FIRST_NIGHT = 3;
	const DEPOSIT_FULL_AMOUNT = 4;

	/**
	 * Cancellation Type
	 */
	const CANCELLATION_NO = 0;
	const CANCELLATION_FIXED_AMOUNT = 1;
	const CANCELLATION_PERCENTAGE = 2;
	const CANCELLATION_FIRST_NIGHT = 3;
	const CANCELLATION_FULL_AMOUNT = 4;

	/**
	 * Qustion Type
	 */
	const QUESTION_YESNO = 0;
	const QUESTION_TEXT = 1;
	const QUESTION_TEXT_FIELD = 2;
	const QUESTION_MULTI = 3;

	public static function getServiceLocator() {
		if ( !isset($GLOBALS['Tls_ServiceLocator']) ) {
			$smConfig = include __DIR__ . '/../config/sm.config.php';
			self::$config = array_merge_recursive(self::$config, $smConfig);
			$smConfig = isset(self::$config['service_manager'])
			    ? self::$config['service_manager'] : array();
			$sc = new \Zend\ServiceManager\Config($smConfig);
			$sm = new \Zend\ServiceManager\ServiceManager($sc);
			$GLOBALS['Tls_ServiceLocator'] = $sm;
		}

		return $GLOBALS['Tls_ServiceLocator'];
	}

	public static function get($name) {
		$sm = self::getServiceLocator();
		return $sm->get($name);
	}
}
