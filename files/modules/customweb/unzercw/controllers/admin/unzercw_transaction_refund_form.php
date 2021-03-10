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
require_once 'Customweb/Payment/Authorization/DefaultInvoiceItem.php';
require_once 'Customweb/Payment/Authorization/IInvoiceItem.php';


class unzercw_transaction_refund_form extends oxAdminDetails
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
			$oOrder = oxNew('oxorder');
			$oOrder->load($orderId);

			$paymentId = $oOrder->oxorder__oxpaymenttype->value;
			if (UnzerCwHelper::isUnzercwPaymentMethod($paymentId)) {
				$transaction = UnzerCwHelper::loadTransaction($oOrder->oxorder__oxtransid->value);

				$this->_aViewData['transactionId'] = $transaction->getTransactionId();

				$this->_aViewData['referer'] = oxRegistry::getConfig()->getRequestParameter('referer');
				$this->_aViewData['refererId'] = oxRegistry::getConfig()->getRequestParameter('refererId');

				$this->_aViewData['isRefundPossible'] = $transaction->getTransactionObject()->isRefundPossible();
				$this->_aViewData['isPartialRefundPossible'] = $transaction->getTransactionObject()->isPartialRefundPossible();
				$this->_aViewData['isRefundClosable'] = array($transaction->getTransactionObject()->isRefundClosable());
				$this->_aViewData['refundableAmount'] = Customweb_Util_Currency::formatAmount($transaction->getTransactionObject()->getRefundableAmount(), $transaction->getTransactionObject()->getCurrencyCode());

				$this->_aViewData['decimalPlaces'] = Customweb_Util_Currency::getDecimalPlaces($transaction->getTransactionObject()->getCurrencyCode());
				$this->_aViewData['currencyCode'] = strtoupper($transaction->getTransactionObject()->getCurrencyCode());

				$nonRefundedLineItems = array();
				foreach ($transaction->getTransactionObject()->getNonRefundedLineItems() as $item) {
					$amountExcludingTax = Customweb_Util_Currency::formatAmount($item->getAmountExcludingTax(), $transaction->getTransactionObject()->getCurrencyCode());
					$amountIncludingTax = Customweb_Util_Currency::formatAmount($item->getAmountIncludingTax(), $transaction->getTransactionObject()->getCurrencyCode());
					if ($item->getType() == Customweb_Payment_Authorization_IInvoiceItem::TYPE_DISCOUNT) {
						$amountExcludingTax = $amountExcludingTax * -1;
						$amountIncludingTax = $amountIncludingTax * -1;
					}

					$nonRefundedLineItems[] = array(
						'name' => UnzerCwHelper::toDefaultEncoding($item->getName()),
						'sku' => UnzerCwHelper::toDefaultEncoding($item->getSku()),
						'type' => $item->getType(),
						'tax_rate' => $item->getTaxRate(),
						'qty' => $item->getQuantity(),
						'amount_excl' => $amountExcludingTax,
						'amount_incl' => $amountIncludingTax
					);
				}
				$this->_aViewData['nonRefundedLineItems'] = $nonRefundedLineItems;

				return "unzercw_transaction_refund_form.tpl";
			}
		}

		return "unzercw_order_transactions_invalid.tpl";
	}

	public function refund()
	{
		$transaction = UnzerCwHelper::loadTransaction(oxRegistry::getConfig()->getRequestParameter('transactionId'));
		$payment = $transaction->getTransactionObject()->getPaymentMethod();

		try {
			if (isset($_POST['quantity'])) {
				$refundLineItems = array();
				$lineItems = $transaction->getTransactionObject()->getNonRefundedLineItems();
				foreach ($_POST['quantity'] as $index => $quantity) {
					if (isset($_POST['price_including'][$index]) && floatval($_POST['price_including'][$index]) != 0) {
						$originalItem = $lineItems[$index];
						$priceModifier = 1;
						if ($originalItem->getType() == Customweb_Payment_Authorization_IInvoiceItem::TYPE_DISCOUNT) {
							$priceModifier = -1;
						}						
						$refundLineItems[$index] = new Customweb_Payment_Authorization_DefaultInvoiceItem (
								$originalItem->getSku(), $originalItem->getName(), $originalItem->getTaxRate(), $priceModifier *floatval($_POST['price_including'][$index]), $quantity, $originalItem->getType()
						);
					}
				}
				if (count($refundLineItems) > 0) {
					$close = false;
					if (isset($_POST['close']) && $_POST['close'] == 'on') {
						$close = true;
					}
					$payment->refund($transaction, $refundLineItems, $close);
				}
			} else {
				$payment->refund($transaction);
			}

			oxRegistry::get("oxUtilsView")->addErrorToDisplay('Refund was successful.');

			if (oxRegistry::getConfig()->getRequestParameter('referer') == 'transaction') {
				return 'unzercw_transaction_view?oxid=' . oxRegistry::getConfig()->getRequestParameter('refererId');
			}
			return 'unzercw_order_transactions?oxid=' . $this->getEditObjectId();
		} catch (Exception $e) {
			oxRegistry::get("oxUtilsView")->addErrorToDisplay($e);
		}
	}
}