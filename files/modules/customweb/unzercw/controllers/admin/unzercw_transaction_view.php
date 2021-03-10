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



class unzercw_transaction_view extends oxAdminDetails
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

		$this->_aViewData["menustructure"] = $this->getNavigation()->getDomXml()->documentElement->childNodes;

		$transactionId = $this->getEditObjectId();
		if ($transactionId != '-1' && isset($transactionId)) {
			$transaction = UnzerCwHelper::loadTransaction($transactionId);

			$this->_aViewData['transaction'] = $transaction;
			$this->_aViewData['transactionId'] = $transaction->getTransactionId();

			if ($transaction->getOrderId() != null) {
				$this->_aViewData['orderId'] = $transaction->getOrderId();
				$this->_aViewData['orderNumber'] = $transaction->getOrder()->oxorder__oxordernr->value;
			}

			$this->_aViewData['paymentInformation'] = (string) $transaction->getTransactionObject()->getPaymentInformation();

			$this->_aViewData['isCapturePossible'] = $transaction->getTransactionObject()->isCapturePossible();
			$this->_aViewData['isPartialCapturePossible'] = $transaction->getTransactionObject()->isPartialCapturePossible();

			$this->_aViewData['isRefundPossible'] = $transaction->getTransactionObject()->isRefundPossible();
			$this->_aViewData['isPartialRefundPossible'] = $transaction->getTransactionObject()->isPartialRefundPossible();

			$this->_aViewData['isCancelPossible'] = $transaction->getTransactionObject()->isCancelPossible();

			$this->_aViewData['isUpdatable'] = $transaction->getTransactionObject()->isUpdatable();

			$labels = $transaction->getTransactionObject()->getTransactionLabels();
			foreach ($labels as $index => $label) {
				$labels[$index]['label'] = UnzerCwHelper::toDefaultEncoding($label['label']);
				$labels[$index]['value'] = UnzerCwHelper::toDefaultEncoding($label['value']);
				$labels[$index]['description'] = UnzerCwHelper::toDefaultEncoding($label['description']);
			}
			$this->_aViewData['labels'] = $labels;

			$items = array();
			foreach ($transaction->getTransactionObject()->getHistoryItems() as $item) {
				$items[] = array(
					'creationdate' => $item->getCreationDate()->format(oxRegistry::getLang()->translateString('DATE_FORMAT')),
					'action' => $item->getActionPerformed(),
					'message' => UnzerCwHelper::toDefaultEncoding($item->getMessage())
				);
			}
			$this->_aViewData['history'] = $items;

			$captures = array();
			foreach ($transaction->getTransactionObject()->getCaptures() as $capture) {
				$captures[] = array(
					'date' => $capture->getCaptureDate()->format(oxRegistry::getLang()->translateString('DATE_FORMAT')),
					'amount' => $capture->getAmount(),
					'status' => UnzerCwHelper::toDefaultEncoding($capture->getStatus()),
					'id' => $capture->getCaptureId()
				);
			}
			$this->_aViewData['captures'] = $captures;

			$refunds = array();
			foreach ($transaction->getTransactionObject()->getRefunds() as $refund) {
				$refunds[] = array(
					'date' => $refund->getRefundedDate()->format(oxRegistry::getLang()->translateString('DATE_FORMAT')),
					'amount' => $refund->getAmount(),
					'status' => UnzerCwHelper::toDefaultEncoding($refund->getStatus()),
					'id' => $refund->getRefundId()
				);
			}
			$this->_aViewData['refunds'] = $refunds;

			$cancels = array();
			$cancelLabels = array();
			foreach ($transaction->getTransactionObject()->getCancels() as $cancel) {
				$cancels[] = $cancel->getCancelLabels();

				if (empty($cancelLabels)) {
					foreach ($cancel->getCancelLabels() as $field) {
						$cancelLabels[] = $field['label'];
					}
				}
			}
			$this->_aViewData['cancels'] = $cancels;
			$this->_aViewData['cancelLabels'] = $cancelLabels;

			return "unzercw_transaction_view.tpl";
		}

		return "unzercw_order_transactions_invalid.tpl";
	}
}