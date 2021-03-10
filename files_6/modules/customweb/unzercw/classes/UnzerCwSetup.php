<?php
/**
 * You are allowed to use this API in your web application.
 *
 * Copyright (C) 2018 by customweb GmbH
 *
 * This program is licenced under the customweb software licence. With the
 * purchase or the installation of the software in your application you
 * accept the licence agreement. The allowed usage is outlined in the
 * customweb software licence which can be found under
 * http://www.sellxed.com/en/software-license-agreement
 *
 * Any modification or distribution is strictly forbidden. The license
 * grants you the installation in one application. For multiuse you will need
 * to purchase further licences at http://www.sellxed.com/shop.
 *
 * See the customweb software licence agreement for more details.
 *
 *
 * @category	Customweb
 * @package		Customweb_UnzerCw
 * @version		1.0.59
 */

UnzerCwHelper::bootstrap();

require_once 'Customweb/Database/Migration/Manager.php';


class UnzerCwSetup
{
	/**
	 * Execute action on activate event
	 */
	public static function onActivate()
	{
		$scriptDir = dirname(dirname(__FILE__)) . '/scripts/';
		$manager = new Customweb_Database_Migration_Manager(UnzerCwHelper::getDriver(), $scriptDir, 'unzercw_schema_version');
		$manager->migrate();

		self::installPaymentMethods();
		UnzerCwHelper::cleanupTransactions();
	}

	/**
	 * Execute action on deactivate event
	 */
	public static function onDeactivate()
	{

	}

	private static function installPaymentMethods()
	{
		$paymentMethods = array(
			'unzercw_przelewy24' => array(
				'name' => 'unzercw_przelewy24',
 				'description' => 'Przelewy24',
 			),
 			'unzercw_wechatpay' => array(
				'name' => 'unzercw_wechatpay',
 				'description' => 'WeChat Pay',
 			),
 			'unzercw_alipay' => array(
				'name' => 'unzercw_alipay',
 				'description' => 'Alipay',
 			),
 			'unzercw_paypal' => array(
				'name' => 'unzercw_paypal',
 				'description' => 'PayPal',
 			),
 			'unzercw_secureinvoice' => array(
				'name' => 'unzercw_secureinvoice',
 				'description' => 'Secure Invoice',
 			),
 			'unzercw_giropay' => array(
				'name' => 'unzercw_giropay',
 				'description' => 'giropay',
 			),
 			'unzercw_unzerbanktransfer' => array(
				'name' => 'unzercw_unzerbanktransfer',
 				'description' => 'Unzer Bank Transfer',
 			),
 			'unzercw_prepayment' => array(
				'name' => 'unzercw_prepayment',
 				'description' => 'Prepayment',
 			),
 			'unzercw_securesepa' => array(
				'name' => 'unzercw_securesepa',
 				'description' => 'Secure SEPA',
 			),
 			'unzercw_sofortueberweisung' => array(
				'name' => 'unzercw_sofortueberweisung',
 				'description' => 'SOFORT',
 			),
 			'unzercw_openinvoice' => array(
				'name' => 'unzercw_openinvoice',
 				'description' => 'Invoice',
 			),
 			'unzercw_creditcard' => array(
				'name' => 'unzercw_creditcard',
 				'description' => 'Credit / Debit Card',
 			),
 			'unzercw_unzerinstallment' => array(
				'name' => 'unzercw_unzerinstallment',
 				'description' => 'Unzer Instalment',
 			),
 			'unzercw_bcmc' => array(
				'name' => 'unzercw_bcmc',
 				'description' => 'Bancontact',
 			),
 			'unzercw_eps' => array(
				'name' => 'unzercw_eps',
 				'description' => 'EPS',
 			),
 			'unzercw_directdebitssepa' => array(
				'name' => 'unzercw_directdebitssepa',
 				'description' => 'Sepa Direct Debits',
 			),
 			'unzercw_ideal' => array(
				'name' => 'unzercw_ideal',
 				'description' => 'iDEAL',
 			),
 		);

		$driver = UnzerCwHelper::getDriver();
		foreach ($paymentMethods as $paymentMethod) {
			$oPayment = oxNew('oxPayment');
			if(!$oPayment->load($paymentMethod['name'])) {
				$driver->query("INSERT INTO `oxpayments` (
					`OXID`, `OXACTIVE`, `OXDESC`, `OXDESC_1`, `OXTOAMOUNT`
				) VALUES (
					>name, 0, >description, >description, 1000000
				)")->execute(array(
						'>name' => $paymentMethod['name'],
						'>description' => $paymentMethod['description']
					));
			}
		}
	}
}