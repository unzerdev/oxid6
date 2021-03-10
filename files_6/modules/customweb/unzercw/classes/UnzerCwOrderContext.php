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

require_once 'Customweb/Payment/Authorization/OrderContext/AbstractDeprecated.php';
require_once 'Customweb/Date/DateTime.php';
require_once 'Customweb/Core/Language.php';
require_once 'Customweb/Core/Util/Rand.php';


class UnzerCwOrderContext extends Customweb_Payment_Authorization_OrderContext_AbstractDeprecated
{
	/**
	 * @var string
	 */
	private $checkoutId = null;

	/**
	 * @var float
	 */
	private $invoiceAmount = 0;

	/**
	 * @var string
	 */
	private $currencyCode = null;

	/**
	 * @var array
	 */
	private $invoiceItems = array();

	/**
	 * @var null|string
	*/
	private $shippingMethod = null;

	/**
	 * @var int
	 */
	private $paymentMethodId = null;

	/**
	 * @var string
	 */
	private $language = null;

	/**
	 * @var string
	 */
	private $customerEmailAddress = null;

	/**
	 * @var int
	 */
	private $customerId = null;

	/**
	 * @var string
	 */
	private $billingFirstName = null;

	/**
	 * @var string
	 */
	private $billingLastName = null;

	/**
	 * @var string
	 */
	private $billingStreet = null;

	/**
	 * @var string
	 */
	private $billingCity = null;

	/**
	 * @var string
	 */
	private $billingPostCode = null;

	/**
	 * @var string
	 */
	private $billingCountryIsoCode = null;

	/**
	 * @var string
	 */
	private $billingSalutation = null;

	/**
	 * @var string
	 */
	private $billingGender = null;

	/**
	 * @var string
	 */
	private $billingCompanyName = null;

	/**
	 * @var DateTime
	 */
	private $billingDateOfBirth = null;

	/**
	 * @var string
	 */
	private $billingState = null;

	/**
	 * @var string
	 */
	private $billingPhoneNumber = null;

	/**
	 * @var string
	 */
	private $shippingFirstName = null;

	/**
	 * @var string
	 */
	private $shippingLastName = null;

	/**
	 * @var string
	 */
	private $shippingStreet = null;

	/**
	 * @var string
	 */
	private $shippingCity = null;

	/**
	 * @var string
	 */
	private $shippingPostCode = null;

	/**
	 * @var string
	 */
	private $shippingCountryIsoCode = null;

	/**
	 * @var string
	 */
	private $shippingSalutation = null;

	/**
	 * @var string
	 */
	private $shippingGender = null;

	/**
	 * @var string
	 */
	private $shippingCompanyName = null;

	/**
	 * @var DateTime
	 */
	private $shippingDateOfBirth = null;

	/**
	 * @var string
	 */
	private $shippingState = null;

	/**
	 * @var string
	 */
	private $shippingPhoneNumber = null;

	/**
	 * @deprecated
	 * @var int
	 */
	protected $orderId = null;

	/**
	 * @var string
	 */
	private $customerOxid = null;

	/**
	 *
	 * @var string
	 */
	private $newCustomer = 'unkown';

	/**
	 *
	 * @var boolean
	 */
	private $isAjaxReloadRequired = false;

	/**
	 * Create order context.
	 *
	 * @param int $orderId
	 */
	public function __construct($order = null, $paymentMethod = null)
	{
		$this->collectOrderData($order, $paymentMethod);

		$method = $this->getPaymentMethod();
		if($method->existsPaymentMethodConfigurationValue('form_position')) {
			$this->isAjaxReloadRequired = $method->getPaymentMethodConfigurationValue('form_position') == 'payment';
		}
	}

	public function isAjaxReloadRequired() {
		return $this->isAjaxReloadRequired;
	}

	/**
	 * Used for old order contexts to work properly.
	 */
	public function __wakeup()
	{
		if ($this->orderId !== null) {
			$order = oxNew('oxorder');
			$order->load($this->orderId);
			$this->collectOrderData($order);
		}
	}

	/**
	 * @param int $orderId
	 * @throws Exception
	 */
	protected function collectOrderData($order = null, $paymentMethod = null)
	{
		$this->invoiceAmount = $order->getTotalOrderSum();
		$this->currencyCode = UnzerCwHelper::toUtf8($order->getOrderCurrency()->name);
		$this->collectInvoiceItems($order);
		$this->shippingMethod = UnzerCwHelper::toUtf8($order->getDelSet()->oxdeliveryset__oxtitle->value);
		$this->paymentMethodId = $order->oxorder__oxpaymenttype->value;
		if ($paymentMethod !== null) {
			$this->paymentMethodId = $paymentMethod->getPaymentMethodId();
		}
		$this->collectLanguage($order);
		$this->customerEmailAddress = UnzerCwHelper::toUtf8($order->oxorder__oxbillemail->value);
		$this->customerOxid = $order->oxorder__oxuserid->value;
		$this->customerId = $this->getCustomer()->oxuser__oxcustnr->value;
		$this->newCustomer = oxDb::getDb()->getOne('SELECT 1 FROM oxorder WHERE oxuserid = ? AND oxorderdate >= ? AND oxtransstatus = \'OK\' LIMIT 1',
				array($this->getCustomer()->getId(), $this->getCustomer()->oxuser__oxregister->value)) > 0 ? 'existing' : 'new';
		$this->orderId = null;

		if (!isset($_SESSION['unzercw_checkout_id'])) {
			$_SESSION['unzercw_checkout_id'] = Customweb_Core_Util_Rand::getUuid();
		}
		$this->checkoutId = $_SESSION['unzercw_checkout_id'];

		$this->collectBillingAddress($order);
		$this->collectShippingAddress($order);
	}

	protected function collectBillingAddress($order)
	{
		$this->billingFirstName = UnzerCwHelper::toUtf8($order->oxorder__oxbillfname->value);
		$this->billingLastName = UnzerCwHelper::toUtf8($order->oxorder__oxbilllname->value);
		$this->billingStreet = UnzerCwHelper::toUtf8($order->oxorder__oxbillstreet->value . ' ' . $order->oxorder__oxbillstreetnr->value);
		$this->billingCity = UnzerCwHelper::toUtf8($order->oxorder__oxbillcity->value);
		$this->billingPostCode = $order->oxorder__oxbillzip->value;

		$countryId = $order->oxorder__oxbillcountryid->value;
		$country = oxNew('oxcountry');
		$country->load($countryId);
		$this->billingCountryIsoCode = UnzerCwHelper::toUtf8($country->oxcountry__oxisoalpha2->value);

		if ($order->oxorder__oxbillsal && $order->oxorder__oxbillsal->value) {
			$this->billingSalutation = UnzerCwHelper::toUtf8($order->oxorder__oxbillsal->value);

			if (strtolower($this->billingSalutation) == 'mr') {
				$this->billingGender = 'male';
			} elseif (strtolower($this->billingSalutation) == 'mrs') {
				$this->billingGender = 'female';
			}
		}

		if ($order->oxorder__oxbillcompany && $order->oxorder__oxbillcompany->value) {
			$this->billingCompanyName = UnzerCwHelper::toUtf8($order->oxorder__oxbillcompany->value);
		}

		$user = $this->getCustomer();
		if ($user->oxuser__oxbirthdate && isset($user->oxuser__oxbirthdate->rawValue)) {
			$value = $user->oxuser__oxbirthdate->rawValue;
			if (!empty($value) && $value != '0000-00-00') {
				$this->billingDateOfBirth = new Customweb_Date_DateTime($value);
			}
		}

		if ($order->oxorder__oxbillstateid && $order->oxorder__oxbillstateid->value) {
			$state = oxNew('oxstate');
			$state->load($order->oxorder__oxbillstateid->value);
			$this->billingState = UnzerCwHelper::toUtf8($state->oxstates__oxisoalpha2->value);
		}

		if ($order->oxorder__oxbillfon && $order->oxorder__oxbillfon->value) {
			$this->billingPhoneNumber = $order->oxorder__oxbillfon->value;
		}
	}

	protected function collectShippingAddress($order)
	{
		if ($order->oxorder__oxdelfname && $order->oxorder__oxdelfname->value) {
			$this->shippingFirstName = UnzerCwHelper::toUtf8($order->oxorder__oxdelfname->value);
		}
		if ($order->oxorder__oxdellname && $order->oxorder__oxdellname->value) {
			$this->shippingLastName =  UnzerCwHelper::toUtf8($order->oxorder__oxdellname->value);
		}
		if ($order->oxorder__oxdelstreet && $order->oxorder__oxdelstreet->value) {
			$this->shippingStreet = UnzerCwHelper::toUtf8($order->oxorder__oxdelstreet->value . ' ' . $order->oxorder__oxdelstreetnr->value);
		}
		if ($order->oxorder__oxdelcity && $order->oxorder__oxdelcity->value) {
			$this->shippingCity = UnzerCwHelper::toUtf8($order->oxorder__oxdelcity->value);
		}
		if ($order->oxorder__oxdelzip && $order->oxorder__oxdelzip->value) {
			$this->shippingPostCode = $order->oxorder__oxdelzip->value;
		}

		if ($order->oxorder__oxdelcountryid && $order->oxorder__oxdelcountryid->value) {
			$countryId = $order->oxorder__oxdelcountryid->value;
			$country = oxNew('oxcountry');
			$country->load($countryId);
			$this->shippingCountryIsoCode = UnzerCwHelper::toUtf8($country->oxcountry__oxisoalpha2->value);
		}

		if ($order->oxorder__oxdelsal && $order->oxorder__oxdelsal->value) {
			$this->shippingSalutation = UnzerCwHelper::toUtf8($order->oxorder__oxdelsal->value);
		}
		if ($order->oxorder__oxdelcompany && $order->oxorder__oxdelcompany->value) {
			$this->shippingCompanyName = UnzerCwHelper::toUtf8($order->oxorder__oxdelcompany->value);
		}
		if (strtolower($this->shippingSalutation) == 'mr') {
			$this->shippingGender = 'male';
		} elseif (strtolower($this->shippingSalutation) == 'mrs') {
			$this->shippingGender = 'female';
		}

		if ($order->oxorder__oxdelstateid && $order->oxorder__oxdelstateid->value) {
			$state = oxNew('oxstate');
			$state->load($order->oxorder__oxdelstateid->value);
			$this->shippingState = UnzerCwHelper::toUtf8($state->oxstates__oxisoalpha2->value);
		}
		if ($order->oxorder__oxdelfon && $order->oxorder__oxdelfon->value) {
			$this->shippingPhoneNumber = $order->oxorder__oxdelfon->value;
		}
	}

	/**
	 * @param Shopware\Models\Order\Order $order
	 */
	protected function collectLanguage($order)
	{
		$langId = $order->getOrderLanguage();
		foreach (oxRegistry::getConfig()->getConfigParam('aLanguageParams') as $code => $lang) {
			if ($lang['baseId'] == $langId) {
				$this->language = UnzerCwHelper::toUtf8($code);
			}
		}
	}

	protected function collectInvoiceItems($order)
	{
		$this->invoiceItems = UnzerCwHelper::getInvoiceItemsFromOrder($order);
	}

	/**
	 * @return oxuser
	 */
	public function getCustomer()
	{
		$user = oxNew('oxuser');
		if ($this->customerOxid !== null) {
			$customerId = $this->customerOxid;
		} else {
			$oDb = oxDb::getDb();
			$result = "select oxid from ".$user->getViewName()." where oxcustnr = ".$oDb->quote( $this->getCustomerId() );
			$customerId = $oDb->getOne($result);
			if (!$customerId) {
				$customerId = $this->getCustomerId();
			}
		}
		$user->load($customerId);
		return $user;
	}

	/**
	 * Is the order recurring?
	 *
	 * @return boolean
	 */
	public function isRecurring()
	{
		return false;
	}

	public function getCheckoutId()
	{
		return $this->checkoutId;
	}

	public function getOrderAmountInDecimals()
	{
		return $this->invoiceAmount;
	}

	public function getCurrencyCode()
	{
		return $this->currencyCode;
	}

	public function getInvoiceItems()
	{
		return $this->invoiceItems;
	}

	public function getShippingMethod()
	{
		return $this->shippingMethod;
	}

	public function getPaymentMethod()
	{
		return new UnzerCwPaymentMethod($this->paymentMethodId);
	}

	public function getLanguage()
	{
		return new Customweb_Core_Language($this->language);
	}

	public function getCustomerEMailAddress()
	{
		return $this->customerEmailAddress;
	}

	public function getBillingFirstName()
	{
		return $this->billingFirstName;
	}

	public function getBillingLastName()
	{
		return $this->billingLastName;
	}

	public function getBillingStreet()
	{
		return $this->billingStreet;
	}

	public function getBillingCity()
	{
		return $this->billingCity;
	}

	public function getBillingPostCode()
	{
		return $this->billingPostCode;
	}

	public function getBillingCountryIsoCode()
	{
		return $this->billingCountryIsoCode;
	}

	public function getBillingSalutation()
	{
		return $this->billingSalutation;
	}

	public function getBillingDateOfBirth()
	{
		return $this->billingDateOfBirth;
	}

	public function getBillingCommercialRegisterNumber()
	{
		return null;
	}

	public function getBillingCompanyName()
	{
		return $this->billingCompanyName;
	}

	public function getBillingSalesTaxNumber()
	{
		return null;
	}

	public function getBillingSocialSecurityNumber()
	{
		return null;
	}

	public function getShippingFirstName()
	{
		if ($this->shippingFirstName !== null) {
			return $this->shippingFirstName;
		}
		return $this->getBillingFirstName();
	}

	public function getShippingLastName()
	{
		if ($this->shippingLastName !== null) {
			return $this->shippingLastName;
		}
		return $this->getBillingLastName();
	}

	public function getShippingStreet()
	{
		if ($this->shippingStreet !== null) {
			return $this->shippingStreet;
		}
		return $this->getBillingStreet();
	}

	public function getShippingCity()
	{
		if ($this->shippingCity !== null) {
			return $this->shippingCity;
		}
		return $this->getBillingCity();
	}

	public function getShippingPostCode()
	{
		if ($this->shippingPostCode !== null) {
			return $this->shippingPostCode;
		}
		return $this->getBillingPostCode();
	}

	public function getShippingCountryIsoCode()
	{
		if ($this->shippingCountryIsoCode !== null) {
			return $this->shippingCountryIsoCode;
		}
		return $this->getBillingCountryIsoCode();
	}

	public function getShippingCompanyName()
	{
		if ($this->shippingCompanyName !== null) {
			return $this->shippingCompanyName;
		}
		return $this->getBillingCompanyName();
	}

	public function getShippingSalutation()
	{
		if ($this->shippingSalutation !== null) {
			return $this->shippingSalutation;
		}
		return $this->getBillingSalutation();
	}

	public function getCustomerId()
	{
		return $this->customerId;
	}

	public function isNewCustomer()
	{
		return $this->newCustomer;
	}

	public function getCustomerRegistrationDate()
	{
		return null;
	}

	public function getBillingEMailAddress()
	{
		return $this->getCustomerEMailAddress();
	}

	public function getBillingGender()
	{
		return $this->billingGender;
	}

	public function getBillingState()
	{
		return $this->billingState;
	}

	public function getBillingPhoneNumber()
	{
		return $this->billingPhoneNumber;
	}

	public function getBillingMobilePhoneNumber()
	{
		return null;
	}

	public function getShippingEMailAddress()
	{
		return $this->getCustomerEMailAddress();
	}

	public function getShippingGender()
	{
		if ($this->shippingGender !== null) {
			return $this->shippingGender;
		}
		return $this->getBillingGender();
	}

	public function getShippingState()
	{
		if ($this->shippingSalutation !== null) {
			return $this->shippingSalutation;
		}
		return $this->getBillingState();
	}

	public function getShippingPhoneNumber()
	{
		if ($this->shippingPhoneNumber !== null) {
			return $this->shippingPhoneNumber;
		}
		return $this->getBillingPhoneNumber();
	}

	public function getShippingMobilePhoneNumber()
	{
		return null;
	}

	public function getShippingDateOfBirth()
	{
		return null;
	}

	public function getShippingCommercialRegisterNumber()
	{
		return null;
	}

	public function getShippingSalesTaxNumber()
	{
		return null;
	}

	public function getShippingSocialSecurityNumber()
	{
		return null;
	}

	public function getOrderParameters()
	{
		return array();
	}
}