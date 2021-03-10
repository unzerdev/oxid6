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

require_once 'Customweb/Payment/BackendOperation/Adapter/Shop/ICapture.php';


/**
 * @Bean
 */
class UnzerCwBackendCaptureAdapter implements Customweb_Payment_BackendOperation_Adapter_Shop_ICapture
{
	public function capture(Customweb_Payment_Authorization_ITransaction $transactionObject)
	{
		$transaction = UnzerCwHelper::loadTransaction($transactionObject->getTransactionId());
		$order = $transaction->getOrder();
		$sDate = date('Y-m-d H:i:s', oxRegistry::get("oxUtilsDate")->getTime());
		$order->oxorder__oxpaid = new oxField($sDate);
		$order->save();
	}

	public function partialCapture(Customweb_Payment_Authorization_ITransaction $transactionObject, $items, $close)
	{
		$transaction = UnzerCwHelper::loadTransaction($transactionObject->getTransactionId());
		$order = $transaction->getOrder();
		$sDate = date('Y-m-d H:i:s', oxRegistry::get("oxUtilsDate")->getTime());
		$order->oxorder__oxpaid = new oxField($sDate);
		$order->save();
	}
}
