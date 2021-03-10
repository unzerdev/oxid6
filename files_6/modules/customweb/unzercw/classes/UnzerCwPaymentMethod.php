<?php
/**
 *  * You are allowed to use this API in your web application.
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
 * @category Customweb
 * @package Customweb_UnzerCw
 * @version 1.0.59
 */

UnzerCwHelper::bootstrap();

require_once 'Customweb/Util/Encoding.php';
require_once 'Customweb/Payment/Authorization/IPaymentMethod.php';
require_once 'Customweb/I18n/Translation.php';
require_once 'Customweb/Core/Logger/Factory.php';


class UnzerCwPaymentMethod extends oxSuperCfg implements Customweb_Payment_Authorization_IPaymentMethod {

	/**
	 * @var Customweb_Core_ILogger
	 */
	private $logger;

	/**
	 *
	 * @var integer
	 */
	private $paymentId;

	/**
	 *
	 * @var string
	 */
	private $paymentMethodName = "";

	/**
	 *
	 * @var string
	 */
	private $paymentMethodDisplayName = "";

	/**
	 * Create a new payment wrapper for the specified payment method.
	 *
	 * @param string|Payment $payment
	 */
	public function __construct($payment){
		if ($payment instanceof oxpayment) {
			$this->paymentMethod = $payment;
			$this->paymentId = $payment->getId();
		}
		else {
			$this->paymentId = $payment;

			$oPayment = oxNew('oxpayment');
			if (!$oPayment->load($payment)) {
				return null;
			}
			$this->paymentMethod = $oPayment;
		}

		$this->paymentMethodDisplayName = $oPayment->oxpayments__oxdesc->value;
		$this->paymentMethodName = substr($this->paymentId, strlen('unzercw_'));

		$this->logger = Customweb_Core_Logger_Factory::getLogger(get_class($this));
	}

	public function getPaymentMethodId(){
		return $this->paymentId;
	}

	public function getPaymentMethodName(){
		return $this->paymentMethodName;
	}

	public function getPaymentMethodDisplayName(){
		return $this->paymentMethodDisplayName;
	}

	public function getPaymentMethod(){
		return $this->paymentMethod;
	}

	public function getPaymentMethodConfigurationValue($key, $languageCode = null){
		return UnzerCwPaymentMethodSetting::getConfigValue($this->paymentId, $key, (string) $languageCode);
	}

	public function existsPaymentMethodConfigurationValue($key, $languageCode = null){
		return UnzerCwPaymentMethodSetting::getConfigValue($this->paymentId, $key, (string) $languageCode) != null;
	}

	public function getOrderContext($order, $payment = null){
		return new UnzerCwOrderContext($order, $payment);
	}

	public function authorizeTransaction(UnzerCwTransaction $transaction){
		$orderId = $transaction->getOrderId();
		if ($transaction->getTransactionObject()->isAuthorized()) {
			$this->logger->logDebug('authorizeTransaction: the authorization of transaction ' . $transaction->getTransactionId() . ' succeeded.');
			if (UnzerCwHelper::isCreateOrderBefore()) {
				$_GET['unzercw_transaction_id'] = $transaction->getTransactionId();
				$oOrder = $transaction->getOrder();
				$oOrder->finishOrder(unserialize(base64_decode($transaction->getSessionData())));
				$this->logger->logDebug('authorizeTransaction: the order ' . $transaction->getOrderId() . ' of transaction ' . $transaction->getTransactionId() . ' has been finished.');
			}
			elseif (empty($orderId)) {
				$sessionBackup = $_SESSION;
				$_SESSION = unserialize(base64_decode($transaction->getSessionData()));

				// Reset basket and user so they are loaded again with data from the transaction session
				$this->getSession()->setBasket(null);
				$this->setUser(null);

				$oOrder = $this->createOrder();
				$this->logger->logDebug('authorizeTransaction: the order ' . $transaction->getOrderId() . ' of transaction ' . $transaction->getTransactionId() . ' has been created.');

				$sessionData = $oOrder->getTmpSessionData();

				$transaction->setOrderId($oOrder->getId());
				$oOrder->oxorder__oxtransid = new oxField($transaction->getTransactionId());
				$oOrder->save();

				$oOrder->finishOrder(unserialize(base64_decode($sessionData)));
				$this->logger->logDebug('authorizeTransaction: the order ' . $transaction->getOrderId() . ' of transaction ' . $transaction->getTransactionId() . ' has been finished.');
			}
			else {
				return;
			}

			$oOrder = $transaction->getOrder(false);
			if ($transaction->getTransactionObject()->isCaptured()) {
				$sDate = date('Y-m-d H:i:s', oxRegistry::get("oxUtilsDate")->getTime());
				$oOrder->oxorder__oxpaid = new oxField($sDate);
				$this->logger->logDebug('authorizeTransaction: the order ' . $transaction->getOrderId() . ' of transaction ' . $transaction->getTransactionId() . ' has been marked as paid.');
			}
			$oOrder->oxorder__oxtransid = new oxField($transaction->getTransactionId());
			$oOrder->save();
			$this->logger->logDebug('authorizeTransaction: the transaction id ' . $transaction->getTransactionId() . ' has been set on the order ' . $transaction->getOrderId() . '.');

			$transaction->setSessionData(null);
			$transaction->setOrderNumber($oOrder->oxorder__oxordernr->value);
			$transaction->setPaymentType($oOrder->oxorder__oxpaymenttype->value);

			if (isset($sessionBackup)) {
				$_SESSION = $sessionBackup;
			}
		}
		if ($transaction->getTransactionObject()->isAuthorizationFailed()) {
			$this->logger->logDebug('authorizeTransaction: the authorization of transaction ' . $transaction->getTransactionId() . ' failed.');
			$transaction->setSessionData(null);
		}
	}

	public function createOrder(){
		$oBasket = $this->getSession()->getBasket();
		$oUser = $this->getUser();
		$_POST['sDeliveryAddressMD5'] = $this->getDeliveryAddressMD5();
		$oOrder = oxNew('oxOrder');
		$this->setAdminMode(true);
		$iSuccess = $oOrder->finalizeOrder($oBasket, $oUser);
		$this->setAdminMode(false);
		$oUser->onOrderExecute($oBasket, $iSuccess);
		return $oOrder;
	}

	/**
	 * Return users setted delivery address md5
	 *
	 * @return string
	 */
	protected function getDeliveryAddressMD5(){
		// bill address
		$oUser = $this->getUser();
		if (method_exists($oUser, 'getEncodedDeliveryAddress')) {
			$sDelAddress = $oUser->getEncodedDeliveryAddress();

			// delivery address
			if (oxRegistry::getSession()->getVariable('deladrid')) {
				$oDelAdress = oxNew('oxaddress');
				$oDelAdress->load(oxRegistry::getSession()->getVariable('deladrid'));

				$sDelAddress .= $oDelAdress->getEncodedDeliveryAddress();
			}

			return $sDelAddress;
		}
		else {
			// bill address
			$sDelAddress = '';

			$sDelAddress .= $oUser->oxuser__oxcompany;
			$sDelAddress .= $oUser->oxuser__oxusername;
			$sDelAddress .= $oUser->oxuser__oxfname;
			$sDelAddress .= $oUser->oxuser__oxlname;
			$sDelAddress .= $oUser->oxuser__oxstreet;
			$sDelAddress .= $oUser->oxuser__oxstreetnr;
			$sDelAddress .= $oUser->oxuser__oxaddinfo;
			$sDelAddress .= $oUser->oxuser__oxustid;
			$sDelAddress .= $oUser->oxuser__oxcity;
			$sDelAddress .= $oUser->oxuser__oxcountryid;
			$sDelAddress .= $oUser->oxuser__oxstateid;
			$sDelAddress .= $oUser->oxuser__oxzip;
			$sDelAddress .= $oUser->oxuser__oxfon;
			$sDelAddress .= $oUser->oxuser__oxfax;
			$sDelAddress .= $oUser->oxuser__oxsal;

			// delivery address
			if (oxRegistry::getSession()->getVariable('deladrid')) {
				$oDelAdress = oxNew('oxaddress');
				$oDelAdress->load(oxRegistry::getSession()->getVariable('deladrid'));

				$sDelAddress .= $oDelAdress->oxaddress__oxcompany;
				$sDelAddress .= $oDelAdress->oxaddress__oxfname;
				$sDelAddress .= $oDelAdress->oxaddress__oxlname;
				$sDelAddress .= $oDelAdress->oxaddress__oxstreet;
				$sDelAddress .= $oDelAdress->oxaddress__oxstreetnr;
				$sDelAddress .= $oDelAdress->oxaddress__oxaddinfo;
				$sDelAddress .= $oDelAdress->oxaddress__oxcity;
				$sDelAddress .= $oDelAdress->oxaddress__oxcountryid;
				$sDelAddress .= $oDelAdress->oxaddress__oxstateid;
				$sDelAddress .= $oDelAdress->oxaddress__oxzip;
				$sDelAddress .= $oDelAdress->oxaddress__oxfon;
				$sDelAddress .= $oDelAdress->oxaddress__oxfax;
				$sDelAddress .= $oDelAdress->oxaddress__oxsal;
			}

			return md5($sDelAddress);
		}
	}

	/**
	 * The transaction is captured with the specified amount.
	 *
	 * @param UnzerCwTransaction $transaction
	 * @param int $amount
	 * @param boolean $close
	 */
	public function capture(UnzerCwTransaction $transaction, $amount = null, $close = true){
		
		$adapter = UnzerCwHelper::createContainer()->getBean('Customweb_Payment_BackendOperation_Adapter_Service_ICapture');
		if ($transaction->getTransactionObject()->isCapturePossible()) {
			if ($transaction->getTransactionObject()->isPartialCapturePossible() && $amount != null) {
				$adapter->partialCapture($transaction->getTransactionObject(), $amount, $close);
			}
			else {
				$adapter->capture($transaction->getTransactionObject());
			}
		}

		if ($transaction->getTransactionObject()->isCaptured()) {
			$order = $transaction->getOrder();
			if ($order instanceof oxOrder) {
				$sDate = date('Y-m-d H:i:s', oxRegistry::get("oxUtilsDate")->getTime());
				$order->oxorder__oxpaid = new oxField($sDate);
				$order->save();
			}
		}

		UnzerCwHelper::getEntityManager()->persist($transaction);

		if (!$transaction->getTransactionObject()->isCaptured()) {
			$errorMessages = $transaction->getTransactionObject()->getErrorMessages();
			throw new Exception('The invoice could not be captured and processed. Reason: ' . (string) end($errorMessages));
		}
		
	}

	/**
	 * Refund money
	 *
	 * @param UnzerCwTransaction $transaction
	 * @param int $amount
	 * @param boolean $close
	 */
	public function refund(UnzerCwTransaction $transaction, $amount = null, $close = true){
		
		$adapter = UnzerCwHelper::createContainer()->getBean('Customweb_Payment_BackendOperation_Adapter_Service_IRefund');
		if ($transaction->getTransactionObject()->isRefundPossible()) {
			if ($transaction->getTransactionObject()->isPartialRefundPossible() && $amount != null) {
				$adapter->partialRefund($transaction->getTransactionObject(), $amount, $close);
			}
			else {
				$adapter->refund($transaction->getTransactionObject());
			}
		}
		else {
			throw new Exception('No refund possible.');
		}

		UnzerCwHelper::getEntityManager()->persist($transaction);
		
	}

	/**
	 * Cancel payment
	 *
	 * @param UnzerCwTransaction $transaction
	 */
	public function cancel(UnzerCwTransaction $transaction){
		
		$adapter = UnzerCwHelper::createContainer()->getBean('Customweb_Payment_BackendOperation_Adapter_Service_ICancel');
		if ($transaction->getTransactionObject()->isCancelPossible()) {
			$adapter->cancel($transaction->getTransactionObject());
		}

		UnzerCwHelper::getEntityManager()->persist($transaction);

		if (!$transaction->getTransactionObject()->isCancelled()) {
			throw new Exception(
					'Canceling of this payment not possible. Reason: ' . (string) end($transaction->getTransactionObject()->getErrorMessages()));
		}
		
	}

	/**
	 * Prevalidate payment method
	 *
	 * @param int $paymentId
	 * @throws Exception
	 */
	protected function preValidate($paymentId){
		$oBasket = oxRegistry::getSession()->getBasket();
		$oldPayment = $oBasket->getPaymentId();
		$oBasket->setPayment($paymentId);
		$order = UnzerCwHelper::getOrderFromBasket();
		$adapter = UnzerCwHelper::getAuthorizationAdapterByContext($this->getOrderContext($order));

		$orderContext = $this->getOrderContext($order);
		$paymentContext = new UnzerCwPaymentCustomerContext($orderContext->getCustomerId());

		try {
			$adapter->preValidate($orderContext, $paymentContext);
		}
		catch (Exception $e) {
			$oBasket->setPayment($oldPayment);
			throw $e;
		}

		$oBasket->setPayment($oldPayment);
	}

	/**
	 * Validate payment method
	 *
	 * @param int $paymentId
	 * @throws Exception
	 */
	protected function validate($paymentId){
		$oBasket = oxRegistry::getSession()->getBasket();
		$oldPayment = $oBasket->getPaymentId();
		$oBasket->setPayment($paymentId);
		$order = UnzerCwHelper::getOrderFromBasket();
		$adapter = UnzerCwHelper::getAuthorizationAdapterByContext($this->getOrderContext($order));

		$orderContext = $this->getOrderContext($order);
		$paymentContext = new UnzerCwPaymentCustomerContext($orderContext->getCustomerId());

		try {
			$adapter->validate($orderContext, $paymentContext, Customweb_Util_Encoding::toUTF8($_REQUEST));
		}
		catch (Exception $e) {
			$oBasket->setPayment($oldPayment);
			throw $e;
		}

		$oBasket->setPayment($oldPayment);
	}

	/**
	 * Validate payment method before showing the list
	 *
	 * @param int $paymentId
	 * @param int $amount
	 * @return boolean
	 */
	public function validateBefore($paymentId){
		$amount = oxRegistry::getSession()->getBasket()->getPriceForPayment();

		$minOrderTotal = $this->getPaymentMethodConfigurationValue('min_order_total');
		if (!empty($minOrderTotal) && is_numeric($minOrderTotal) && $amount < $minOrderTotal) {
			return false;
		}

		$maxOrderTotal = $this->getPaymentMethodConfigurationValue('max_order_total');
		if (!empty($maxOrderTotal) && is_numeric($maxOrderTotal) && $amount > $maxOrderTotal) {
			return false;
		}

		try {
			$this->preValidate($paymentId);
		}
		catch (Exception $e) {
			return false;
		}

		return true;
	}

	/**
	 * Validate payment method after selection
	 *
	 * @param int $paymentId
	 */
	
	public function validateAfter($paymentId){
		if (false) {
			throw new Exception(
					Customweb_I18n_Translation::__(
							'We experienced a problem with your sellxed payment extension. For more information, please visit the configuration page of the plugin.'));
		}

		if (UnzerCwHelper::isPaymentFormOnPaymentPage($this) &&
				 $this->getPaymentMethodConfigurationValue('authorizationMethod') != 'HiddenAuthorization' &&
				 $this->getPaymentMethodConfigurationValue('authorizationMethod') != 'AjaxAuthorization' &&
				 $this->getPaymentMethodConfigurationValue('authorizationMethod') != 'ServerAuthorization') {
			$this->validate($paymentId);
		}
	}
	
}
