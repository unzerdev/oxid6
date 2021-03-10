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

require_once 'Customweb/Util/Currency.php';


class unzercw_transaction_capture extends oxAdminDetails
{
	/**
	 * Executes parent method parent::render(), creates oxorder and
	 * oxlist objects, passes it's data to Smarty engine and returns
	 * name of template file "user_remark.tpl".
	 *
	 * @return string
	 */
	public function render()
	{
		parent::render();

		$orderId = $this->getEditObjectId();
		if ($orderId != '-1' && isset( $orderId)) {
		
			$referer = oxRegistry::getConfig()->getRequestParameter('referer');
			$refererId = oxRegistry::getConfig()->getRequestParameter('refererId');
			if($referer == 'transaction' && $refererId != null){
				$transaction = UnzerCwHelper::loadTransaction($refererId);
				$orderId = $transaction->getOrder()->getId();
			}
			
			
			$oOrder = oxNew('oxorder');
			$oOrder->load($orderId);

			$paymentId = $oOrder->oxorder__oxpaymenttype->value;
			if (UnzerCwHelper::isUnzercwPaymentMethod($paymentId)) {
				$transaction = UnzerCwHelper::loadTransaction($oOrder->oxorder__oxtransid->value);

				$this->_aViewData['transactionId'] = $transaction->getTransactionId();

				$this->_aViewData['referer'] = $referer;
				$this->_aViewData['refererId'] = $refererId;

				$capture = null;
				foreach ($transaction->getTransactionObject()->getCaptures() as $item) {
					if ($item->getCaptureId() == oxRegistry::getConfig()->getRequestParameter('capture')) {
						$capture = $item;
						break;
					}
				}

				$this->_aViewData['captureId'] = $capture->getCaptureId();

				$this->_aViewData['captureLabels'] = $capture->getCaptureLabels();

				$items = array();
				foreach ($capture->getCaptureItems() as $item) {
					$items[] = array(
						'name' => $item->getName(),
						'sku' => $item->getSku(),
						'qty' => $item->getQuantity(),
						'tax_rate' => $item->getTaxRate(),
						'amount_excl' => Customweb_Util_Currency::formatAmount($item->getAmountExcludingTax(), $transaction->getCurrency()),
						'amount_incl' => Customweb_Util_Currency::formatAmount($item->getAmountIncludingTax(), $transaction->getCurrency())
					);
				}
				$this->_aViewData['captureItems'] = $items;

				return "unzercw_transaction_capture.tpl";
			}
		}

		return "unzercw_order_transactions_invalid.tpl";
	}
}