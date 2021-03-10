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

require_once 'Customweb/Payment/Authorization/ITransactionContext.php';
require_once 'Customweb/Payment/Authorization/Hidden/ITransactionContext.php';
require_once 'Customweb/Payment/Authorization/Server/ITransactionContext.php';
require_once 'Customweb/Payment/Authorization/Ajax/ITransactionContext.php';
require_once 'Customweb/Payment/Authorization/PaymentPage/ITransactionContext.php';
require_once 'Customweb/Payment/Authorization/Iframe/ITransactionContext.php';
require_once 'Customweb/Payment/Authorization/Widget/ITransactionContext.php';


class UnzerCwTransactionContext implements Customweb_Payment_Authorization_ITransactionContext,
	Customweb_Payment_Authorization_PaymentPage_ITransactionContext,
	Customweb_Payment_Authorization_Hidden_ITransactionContext,
	Customweb_Payment_Authorization_Iframe_ITransactionContext,
	Customweb_Payment_Authorization_Server_ITransactionContext,
	Customweb_Payment_Authorization_Ajax_ITransactionContext,
	Customweb_Payment_Authorization_Widget_ITransactionContext
{
	/**
	 * @transient
	 * @var UnzerCwTransaction
	 */
	private $transaction = null;

	/**
	 * @transient
	 * @var UnzerCwTransaction
	 */
	private $alias = null;

	/**
	 * @var integer
	 */
	protected $transactionId;

	/**
	 * @var string
	 */
	protected $orderId = null;

	/**
	 *
	 * @var string
	 */
	protected $shopId = null;

	/**
	 * @var UnzerCwOrderContext
	 */
	protected $orderContext;

	/**
	 * @var UnzerCwPaymentCustomerContext
	 */
	protected $customerContext;

	/**
	 * @var string|integer
	 */
	protected $aliasTransactionId = null;

	/**
	 * @var string
	 */
	protected $capturingMode;

	/**
	 * Create a transaction context.
	 *
	 * @param UnzerCwTransactionContext $transaction
	 * @param oxOrder $order
	 * @param oxPayment $paymentMethod
	 * @param string|integer $aliasTransactionId
	 */
	public function __construct(UnzerCwTransaction $transaction, oxOrder $order, UnzerCwPaymentMethod $paymentMethod, $aliasTransactionId = NULL)
	{
		$this->transaction = $transaction;
		$this->transactionId = $transaction->getTransactionId();
		if ($order->oxorder__oxordernr && $order->oxorder__oxordernr->value) {
			$this->orderId = $order->oxorder__oxordernr->value;
		}
		if ($order->oxorder__oxshopid && $order->oxorder__oxshopid->value) {
			$this->shopId = $order->oxorder__oxshopid->value;
		}
		$this->orderContext = new UnzerCwOrderContext($order);
		$this->customerContext = UnzerCwHelper::loadCustomerContext($this->orderContext->getCustomerId());
		$this->capturingMode = $paymentMethod->getPaymentMethodConfigurationValue('capturing');

		if ($paymentMethod->getPaymentMethodConfigurationValue('alias_manager') == 'active') {
			if ($aliasTransactionId === NULL || $aliasTransactionId === 'new') {
				$this->aliasTransactionId = 'new';
			} else {
				$this->aliasTransactionId = intval($aliasTransactionId);
			}
		}

		unset($_SESSION['unzercw_checkout_id']);
	}

	public function __sleep() {
		return array('transactionId', 'orderId', 'shopId', 'capturingMode', 'aliasTransactionId', 'customerContext', 'orderContext');
	}

	public function getOrderContext()
	{
		return $this->orderContext;
	}

	public function getTransactionId()
	{
		return $this->transactionId;
	}

	public function getOrderId()
	{
		return $this->orderId;
	}

	public function isOrderIdUnique()
	{
		$setting = oxRegistry::getConfig()->getShopConfVar('unzercw_order_id', null, 'module:unzercw');
		if ($setting == 'enforce') {
			return true;
		}
		else if ($setting == 'duplicate') {
			return false;
		}
		foreach (oxRegistry::getConfig()->getShopIds() as $shopId) {
			if (oxRegistry::getConfig()->getShopConfVar('blSeparateNumbering', $shopId)) {
				return false;
			}
		}
		return true;
	}

	/**
	 * @return UnzerCwTransaction
	 */
	public function getTransaction()
	{
		if ($this->transaction === NULL) {
			$this->transaction = UnzerCwHelper::loadTransaction($this->getInternalTransactionId());
		}
		return $this->transaction;
	}

	/**
	 * @return number
	 */
	public function getInternalTransactionId()
	{
		return $this->transactionId;
	}

	public function getCapturingMode()
	{
		return null;
	}

	public function getAlias()
	{
		if ($this->getOrderContext()->getPaymentMethod()->getPaymentMethodConfigurationValue('alias_manager') !== 'active') {
			return null;
		}

		if ($this->aliasTransactionId === 'new') {
			return 'new';
		}

		if ($this->aliasTransactionId !== null) {
			if ($this->alias == null) {
				$alias = UnzerCwHelper::loadTransaction($this->aliasTransactionId);
				$this->alias = $alias->getTransactionObject();
			}
			return $this->alias;
		}

		return null;
	}

	public function setAlias($aliasTransactionId)
	{
		$this->aliasTransactionId = $aliasTransactionId;
		$this->alias = null;
		return $this;
	}

	public function createRecurringAlias()
	{
		return $this->getOrderContext()->isRecurring();
	}

	public function getCustomParameters()
	{
		return array(
			'cstrxid' => $this->getInternalTransactionId(),
			//oxRegistry::getSession()->getName() => oxRegistry::getSession()->getId(),
			//'rtoken' => oxRegistry::getSession()->getRemoteAccessToken()
		);
	}

	public function getPaymentCustomerContext()
	{
		return $this->customerContext;
	}

	protected function getProcessUrl()
	{
		return UnzerCwHelper::getUrl(array(
			'cl' => 'unzercw_process'
		));
	}

	public function getNotificationUrl()
	{
		return $this->getProcessUrl();
	}

	public function getSuccessUrl()
	{
		return UnzerCwHelper::getUrl(array(
			'cl' => 'unzercw_process',
			'fnc' => 'success'
		));
	}

	public function getFailedUrl()
	{
		return UnzerCwHelper::getUrl(array(
			'cl' => 'unzercw_process',
			'fnc' => 'fail'
		));
	}

	public function getIframeBreakOutUrl()
	{
		return UnzerCwHelper::getUrl(array(
			'cl' => 'unzercw_breakout'
		));
	}

	public function getJavaScriptSuccessCallbackFunction()
	{
		return "function(url){window.location = url;}";
	}

	public function getJavaScriptFailedCallbackFunction()
	{
		return "function(url){window.location = url;}";
	}
}