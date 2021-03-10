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

require_once 'Customweb/I18n/Translation.php';


class unzercw_payment extends oxUBase
{
	protected $_sThisTemplate = 'unzercw_payment.tpl';

	public function render()
	{
		parent::render();

		try {
			$transaction = UnzerCwHelper::loadTransaction(oxRegistry::getConfig()->getRequestParameter( 'unzercw_transaction_id' ));
			if (UnzerCwHelper::isCreateOrderBefore()) {
				$order = $transaction->getOrder();
			} else {
				$order = UnzerCwHelper::getOrderFromBasket();
			}
			$adapter = UnzerCwHelper::getCheckoutAdapterByContext($transaction->getTransactionObject()->getTransactionContext()->getOrderContext());
			$adapter->prepare($order, $transaction->getTransactionObject()->getTransactionContext()->getOrderContext()->getPaymentMethod(), null, $transaction);

			$vars = $adapter->getConfirmationPageVariables();

			if ($adapter instanceof UnzerCwAdapterPaymentPageAdapter && (!isset($vars['visibleFormFields']) || empty($vars['visibleFormFields']))) {
				header("Location: " . $vars['formActionUrl']);
				die();
			}

			foreach ($vars as $key => $value) {
				$this->_aViewData[$key] = $value;
			}
			$this->_aViewData['visibleFormFields'] = UnzerCwHelper::toDefaultEncoding($this->_aViewData['visibleFormFields']);

			$this->_aViewData['aliasUrl'] = UnzerCwHelper::getUrl(array(
				'cl' => 'unzercw_alias',
			));
			$this->_aViewData['previousUrl'] = UnzerCwHelper::getUrl(array(
				'cl' => 'unzercw_payment',
				'fnc' => 'cancel',
				'unzercw_transaction_id' => $transaction->getTransactionId()
			));
			$this->_aViewData['selfUrl'] = html_entity_decode($this->_aViewData['oViewConf']->getSslSelfLink());
			$this->_aViewData['preventDefault'] = true;
			$this->_aViewData['processingLabel'] = Customweb_I18n_Translation::__('Processing...');
			$this->_aViewData['transactionId'] = $adapter->getTransaction()->getTransactionId();

			return $this->_sThisTemplate;
		} catch (Exception $e) {
			oxRegistry::get("oxUtilsView")->addErrorToDisplay('Unfortunately, there has been a problem during the payment process. Please try again.');

			$redirectionUrl = UnzerCwHelper::getUrl(array(
				'cl' => 'order',
			));

			header("Location: " . $redirectionUrl);
			die();
		}
	}

	public function cancel()
	{
		$transaction = UnzerCwHelper::loadTransaction(oxRegistry::getConfig()->getRequestParameter( 'unzercw_transaction_id' ));
		$transaction->getTransactionObject()->setAuthorizationFailed('The transaction was cancelled by the customer.');
		UnzerCwHelper::getEntityManager()->persist($transaction);

		$redirectionUrl = UnzerCwHelper::getUrl(array(
			'cl' => 'order',
		));
		header("Location: " . $redirectionUrl);
		die();
	}

	public function pay()
	{
		$transaction = UnzerCwHelper::loadTransaction(oxRegistry::getConfig()->getRequestParameter( 'unzercw_transaction_id' ));
		$order = UnzerCwHelper::getOrderFromBasket();
		if ($transaction->getOrder() !== null) {
			$order = $transaction->getOrder();
		}
		$adapter = UnzerCwHelper::getCheckoutAdapterByContext($transaction->getTransactionObject()->getTransactionContext()->getOrderContext());
		$adapter->prepare($order, $transaction->getTransactionObject()->getTransactionContext()->getOrderContext()->getPaymentMethod(), null, $transaction);

		$interfaceClass = $adapter->getPaymentAdapterInterfaceName();
		$return['authorizationMethod'] = $interfaceClass::AUTHORIZATION_METHOD_NAME;

		$vars = $adapter->processOrderConfirmationRequest();
		foreach ($vars as $key => $value) {
			$return[$key] = $value;
		}

		echo json_encode($return);
		die();
	}
}