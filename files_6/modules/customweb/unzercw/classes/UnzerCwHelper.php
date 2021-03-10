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

require_once 'Customweb/Database/Driver/MySQL/Driver.php';
require_once 'Customweb/Util/System.php';
require_once 'Customweb/Payment/Authorization/DefaultInvoiceItem.php';
require_once 'Customweb/DependencyInjection/Container/Default.php';
require_once 'Customweb/Mvc/Template/Smarty/ContainerBean.php';
require_once 'Customweb/Util/Invoice.php';
require_once 'Customweb/Database/Entity/Manager.php';
require_once 'Customweb/Payment/Endpoint/Dispatcher.php';
require_once 'Customweb/Cache/Backend/Memory.php';
require_once 'Customweb/Asset/Resolver/Simple.php';
require_once 'Customweb/Core/Logger/Factory.php';
require_once 'Customweb/Util/Encoding.php';
require_once 'Customweb/Storage/IBackend.php';
require_once 'Customweb/Core/String.php';
require_once 'Customweb/DependencyInjection/Bean/Provider/Annotation.php';
require_once 'Customweb/I18n/Translation.php';
require_once 'Customweb/DependencyInjection/Bean/Provider/Editable.php';
require_once 'Customweb/Core/Util/Class.php';
require_once 'Customweb/Payment/Authorization/IAdapterFactory.php';


class UnzerCwHelper
{
	private static $container = null;

	private static $entityManager = null;

	private static $driver = null;

	private static $endpointDispatcher = null;

	private static $bootstrapped = false;

	/**
	 * @return Customweb_DependencyInjection_Container_Default
	 */
	public static function createContainer() {
		if (self::$container === null) {
			$packages = array(
			0 => 'Customweb_Unzer',
 			1 => 'Customweb_Payment_Authorization',
 		);
			$packages[] = 'Customweb_Storage_';
			$packages[] = 'Customweb_Payment_Alias_Handler';
			$packages[] = 'Customweb_Payment_TransactionHandler';
			$packages[] = 'Customweb_Payment_SettingHandler';
			$packages[] = 'Customweb_Mvc_Template_Smarty_Renderer';
			$packages[] = 'Customweb_Payment_Update_ContainerHandler';

			$classesPath = dirname(__FILE__);
			foreach(scandir($classesPath) as $file) {
				if (is_file($classesPath . '/' . $file) && self::endsWith($file, '.php')) {
					$packages[] = substr($file, 0, -4);
				}
			}

			$provider = new Customweb_DependencyInjection_Bean_Provider_Editable(new Customweb_DependencyInjection_Bean_Provider_Annotation(
					$packages
			));
			$provider
				->addObject(self::getEntityManager())
				->addObject(self::getDriver())
				->addObject(UnzerCwContextRequest::getInstance())
				->addObject(self::getAssetResolver())
				->addObject(self::getSmartyContainerBean())
				->add('databaseTransactionClassName', 'UnzerCwTransaction')
				->add('storageDatabaseEntityClassName', 'UnzerCwStorage');

			self::$container = new Customweb_DependencyInjection_Container_Default($provider);
		}

		return self::$container;
	}

	/**
	 * @return Customweb_Storage_IBackend
	 */
	public static function getStorage() {
		$storage = self::createContainer()->getBean('Customweb_Storage_IBackend');
		if (!($storage instanceof Customweb_Storage_IBackend)) {
			throw new Exception("Storage must be of type 'Customweb_Storage_IBackend'");
		}
		return $storage;
	}

	/**
	 * @return Customweb_Mvc_Template_Smarty_ContainerBean
	 */
	private static function getSmartyContainerBean()
	{
		$smarty = clone oxRegistry::get("oxUtilsView")->getSmarty();
		$smarty->left_delimiter = '{';
		$smarty->right_delimiter = '}';
		return new Customweb_Mvc_Template_Smarty_ContainerBean($smarty);
	}

	/**
	 *
	 * @return Customweb_Mvc_Template_IRenderer
	 */
	public static function getTemplateRenderer(){
		return self::createContainer()->getBean('Customweb_Mvc_Template_IRenderer');
	}

	/**
	 * @return Customweb_Asset_IResolver
	 */
	public static function getAssetResolver()
	{
		$oModule = oxNew("oxmodule");
		$sModulePath = $oModule->getModulePath('unzercw');
		$path = oxRegistry::getConfig()->getModulesDir() . $sModulePath . '/out/assets/';

		$url = str_replace(
			rtrim(oxRegistry::getConfig()->getConfigParam('sShopDir'), '/'),
			rtrim(oxRegistry::getConfig()->getCurrentShopUrl( false ), '/'),
			$path
		);
		return new Customweb_Asset_Resolver_Simple($path, $url);
	}

	/**
	 * @return Customweb_Database_Entity_Manager
	 */
	public static function getEntityManager() {
		if (self::$entityManager === null) {
			$cache = new Customweb_Cache_Backend_Memory();
			self::$entityManager = new Customweb_Database_Entity_Manager(self::getDriver(), $cache);
		}
		return self::$entityManager;
	}

	/**
	 * @return Customweb_Database_Driver_PDO_Driver
	 */
	public static function getDriver() {
		if (self::$driver === null) {
			$connectionInstance = \OxidEsales\EshopCommunity\Core\DatabaseProvider::getMaster();
			$pdoConnection = UnzerCwDatabaseConnectionWrapper::getInnerConnection($connectionInstance)->getWrappedConnection();
			if (!($pdoConnection instanceof \Doctrine\DBAL\Driver\PDOConnection)) {
				throw new Exception('Only PDO database connections are supported.');
			}

			self::$driver = new UnzerCwDatabaseDriver($pdoConnection);
		}
		return self::$driver;
	}

	/**
	 * @return Customweb_Payment_Endpoint_Dispatcher
	 */
	public static function getEndpointDispatcher()
	{
		if (self::$endpointDispatcher == null) {
			$container = self::createContainer();
			$packages = array(
			0 => 'Customweb_Unzer',
 			1 => 'Customweb_Payment_Authorization',
 		);
			$adapter = new UnzerCwEndpointAdapter();

			self::$endpointDispatcher = new Customweb_Payment_Endpoint_Dispatcher($adapter, $container, $packages);
		}
		return self::$endpointDispatcher;
	}

	/**
	 * @return string
	 */
	public static function getUploadDirectory()
	{
		return oxRegistry::getConfig()->getConfigParam('sShopDir') . '/out/unzercw/';
	}

	protected static function getAuthorizationAdapterFactory() {
		$container = self::createContainer();
		$factory = $container->getBean('Customweb_Payment_Authorization_IAdapterFactory');

		if (!($factory instanceof Customweb_Payment_Authorization_IAdapterFactory)) {
			throw new Exception("The payment api has to provide a class which implements 'Customweb_Payment_Authorization_IAdapterFactory' as a bean.");
		}

		return $factory;
	}

	public static function getAuthorizationAdapter($authorizationMethodName) {
		return self::getAuthorizationAdapterFactory()->getAuthorizationAdapterByName($authorizationMethodName);
	}

	public static function getAuthorizationAdapterByContext(Customweb_Payment_Authorization_IOrderContext $orderContext) {
		return self::getAuthorizationAdapterFactory()->getAuthorizationAdapterByContext($orderContext);
	}

	/**
	 * @param Customweb_Payment_Authorization_IOrderContext $orderContext
	 * @return UnzerCwAdapterIAdapter
	 */
	public static function getCheckoutAdapterByContext(Customweb_Payment_Authorization_IOrderContext $orderContext) {
		$adapter = self::getAuthorizationAdapterByContext($orderContext);
		return self::getCheckoutAdapter($adapter);
	}

	/**
	 * @param string $method
	 * @return UnzerCwAdapterIAdapter
	 */
	public static function getCheckoutAdapterByAuthorizationMethod($method) {
		$adapter = self::getAuthorizationAdapterFactory()->getAuthorizationAdapterByName($method);
		return self::getCheckoutAdapter($adapter);
	}

	/**
	 * @param Customweb_Payment_Authorization_IAdapter $paymentAdapter
	 * @throws Exception
	 * @return UnzerCwAdapterIAdapter
	 */
	public static function getCheckoutAdapter(Customweb_Payment_Authorization_IAdapter $paymentAdapter) {
		$reflection = new ReflectionClass($paymentAdapter);
		$adapters = self::createContainer()->getBeansByType('UnzerCwAdapterIAdapter');
		foreach ($adapters as $adapter) {
			if ($adapter instanceof UnzerCwAdapterIAdapter) {
				$inferfaceName = $adapter->getPaymentAdapterInterfaceName();
				try {
					Customweb_Core_Util_Class::loadLibraryClassByName($inferfaceName);
					if ($reflection->implementsInterface($inferfaceName)) {
						$adapter->setInterfaceAdapter($paymentAdapter);
						return $adapter;
					}
				}
				catch(Customweb_Core_Exception_ClassNotFoundException $e) {
					// Ignore
				}
			}
		}

		throw new Exception("Could not resolve to checkout adapter.");
	}

	public static function bootstrap()
	{
		if (self::$bootstrapped === false) {
			set_include_path(implode(PATH_SEPARATOR, array(
				get_include_path(),
				realpath(dirname(__FILE__)),
			)));

			require_once dirname(__FILE__) . '/../../../../lib/loader.php';

			require_once 'Customweb/I18n/Translation.php';
			Customweb_I18n_Translation::getInstance()->addResolver(new UnzerCwTranslationResolver());

			require_once 'Customweb/Core/Logger/Factory.php';
			if (class_exists('UnzerCwLoggingListener')) {
				Customweb_Core_Logger_Factory::addListener(new UnzerCwLoggingListener());
			}

			self::$bootstrapped = true;
		}
	}

	public static function isoToUtf8($str)
	{
		if (!oxRegistry::getConfig()->getShopConfVar('iUtfMode')) {
			return Customweb_Core_String::_($str, 'ISO-8859-15')->toString();
		}
		return $str;
	}

	public static function toUtf8($str)
	{
		if (!oxRegistry::getConfig()->getShopConfVar('iUtfMode')) {
			return Customweb_Util_Encoding::toUTF8($str);
		}
		return $str;
	}

	public static function toDefaultEncoding($str)
	{
		if (!oxRegistry::getConfig()->getShopConfVar('iUtfMode')) {
			return utf8_decode($str);
		}
		return $str;
	}

	public static function isUnzercwPaymentMethod($paymentId)
	{
		return strpos($paymentId, 'unzercw') === 0;
	}

	public static function isCreateOrderBefore()
	{
		return (oxRegistry::getConfig()->getShopConfVar('unzercw_order_creation', null, 'module:unzercw') == 'before');
	}

	public static function isPaymentFormOnPaymentPage($paymentMethod)
	{
		return $paymentMethod->getPaymentMethodConfigurationValue('form_position') == 'payment';
	}

	public static function isPaymentFormOnCheckoutPage($paymentMethod)
	{
		return $paymentMethod->getPaymentMethodConfigurationValue('form_position') == 'checkout';
	}

	public static function isPaymentFormOnSeparatePage($paymentMethod)
	{
		return $paymentMethod->getPaymentMethodConfigurationValue('form_position') == 'separate';
	}

	public static function isDeleteOrderOnFailedAuthorization()
	{
		return (oxRegistry::getConfig()->getShopConfVar('unzercw_delete_failed_orders', null, 'module:unzercw') == 'yes');
	}

	public static function loadAliasesForCustomer(UnzerCwOrderContext $orderContext)
	{
		/* @var $handler Customweb_Payment_Alias_Handler */
		$handler = self::createContainer()->getBean('Customweb_Payment_Alias_Handler');
		return $handler->getAliasTransactions($orderContext);
	}

	public static function getOrderFromBasket()
	{
		$order = oxNew('oxorder');
		$order->simulate(oxRegistry::getSession()->getBasket(), oxRegistry::getConfig()->getUser());
		return $order;
	}

	public static function getUrl($parameters, $baseUrl = false)
	{
		if ($baseUrl == false) {
			$baseUrl = oxRegistry::getConfig()->getSslShopUrl();
		}
		$url = oxRegistry::get("oxUtilsUrl")->appendUrl(
			$baseUrl,
			$parameters
		);
		$url = oxRegistry::get("oxUtilsUrl")->processUrl($url);

		$url = str_replace('&amp;', '&', $url);

		$parts = explode('?', $url);
		if (count($parts) == 2) {
			$query = array();
			parse_str($parts[1], $query);

			$cleanQuery = array();
			foreach ($query as $key => $value) {
				if (!in_array($key, array('force_sid', 'force_admin_sid', 'stoken'))) {
					$cleanQuery[$key] = $value;
				}
			}

			$url = $parts[0] . '?' . http_build_query($cleanQuery, '', '&');
		}

		return $url;
	}

	/**
	 * Load and return a transaction object by transaction id or temporary order.
	 *
	 * @param string $transactionId
	 * @throws Exception
	 * @return UnzerCwTransaction
	 */
	public static function loadTransaction($transactionId)
	{
		try {
			$transaction = self::getEntityManager()->fetch('UnzerCwTransaction', $transactionId);
		} catch (Customweb_Database_Entity_Exception_EntityNotFoundException $e) {
			$transaction = null;
		}
		if (!($transaction instanceof UnzerCwTransaction) || $transaction->getTransactionObject() == null) {
			throw new Exception("Not a valid transaction provided");
		}
		return $transaction;
	}

	public static function loadTransactionByPaymentId($paymentId)
	{
		$transactions = self::getEntityManager()->searchByFilterName('UnzerCwTransaction', 'loadByPaymentId', array('>paymentId' => $paymentId));
		if (!empty($transactions)) {
			$transaction = end($transactions);
		}

		if (!($transaction instanceof UnzerCwTransaction) || $transaction->getTransactionObject() == null) {
			throw new Exception("Not a valid transaction provided");
		}

		return $transaction;
	}

	public static function loadCustomerContext($customerId)
	{
		$paymentContexts = self::getEntityManager()->searchByFilterName('UnzerCwPaymentCustomerContext', 'loadByCustomerId', array('>customerId' => $customerId));
		if (!empty($paymentContexts)) {
			$paymentContext = end($paymentContexts);
		} else {
			$paymentContext = new UnzerCwPaymentCustomerContext();
			$paymentContext->setCustomerId($customerId);
			self::getEntityManager()->persist($paymentContext);
		}
		return $paymentContext;
	}

	public static function waitForNotification($transactionId)
	{
		$maxTime = Customweb_Util_System::getMaxExecutionTime() - 2;

		if ($maxTime > 30) {
			$maxTime = 30;
		}

		$startTime = microtime(true);
		while(true){
			$redirectionUrl = self::handleTransactionStatus($transactionId);
			if ($redirectionUrl !== false) {
				return $redirectionUrl;
			}

			if (microtime(true) - $startTime >= $maxTime) {
				break;
			}
			sleep(2);
		}

		return self::getUrl(array(
			'cl' => 'unzercw_pending',
			'cstrxid' => $transactionId
		));
	}

	public static function handleTransactionStatus($transactionId)
	{
		try {
			$transaction = self::getEntityManager()->fetch('UnzerCwTransaction', $transactionId, false);
		} catch (Customweb_Database_Entity_Exception_EntityNotFoundException $e) {
			$transaction = null;
		}
		if ($transaction instanceof UnzerCwTransaction && $transaction->getTransactionObject() != null) {
			if ($transaction->getTransactionObject()->isAuthorized() && $transaction->getOrderId() != null) {
				oxRegistry::getSession()->setVariable('sess_challenge', $transaction->getOrderId());
				return self::getUrl(array(
					'cl' => 'thankyou',
				));
			}
			if ($transaction->getTransactionObject()->isAuthorizationFailed()) {
				$errorMessages = $transaction->getTransactionObject()->getErrorMessages();
				$messageToDisplay = nl2br((string) end($errorMessages));
				reset($errorMessages);

				oxRegistry::get("oxUtilsView")->addErrorToDisplay($messageToDisplay);

				return self::getUrl(array(
					'cl' => 'order',
				));
			}
		}
		return false;
	}

	public static function cleanupTransactions()
	{
		try {
			$sql = 'UPDATE `unzercw_transaction` SET `sessionData` = \'\' WHERE (DATEDIFF(NOW(), `updatedOn`) >= 2 OR (`authorizationStatus` != \'pending\' AND `authorizationStatus` IS NOT NULL)) AND `sessionData` != \'Tjs=\'';
			oxDb::getDb()->Execute($sql);
		} catch (Exception $e) {}
	}

	public static function getFileOptions($fileTypes = null)
	{
		$dir = UnzerCwHelper::getUploadDirectory();
		if (!file_exists($dir)) {
			return array();
		}

		$fileTypeRegex = '/.*/';
		if (!empty($fileTypes)) {
			$fileTypeRegex = '/\.(' . $fileTypes . ')$/i';
		}

		$fileOptions = array();
		foreach (scandir($dir) as $file) {
			if (is_file($dir . $file) && preg_match($fileTypeRegex, $file)) {
				$fileOptions[] = $file;
			}
		}
		return $fileOptions;
	}

	public static function getCountryByAddress(Customweb_Payment_Authorization_OrderContext_IAddress $address)
	{
		$countryCode = $address->getCountryIsoCode();
		if (!empty($countryCode)) {
			$country = oxNew('oxcountry');
			$countryId = $country->getIdByCode($countryCode);
			$country->load($countryId);
			return $country;
		} else {
			return null;
		}
	}

	public static function getUserAddressArray(oxUser $user)
	{
		return array(
			'oxuser__oxsal' => $user->oxuser__oxsal->value,
			'oxuser__oxfname' => $user->oxuser__oxfname->value,
			'oxuser__oxlname' => $user->oxuser__oxlname->value,
			'oxuser__oxcompany' => $user->oxuser__oxcompany->value,
			'oxuser__oxstreet' => $user->oxuser__oxstreet->value,
			'oxuser__oxstreetnr' => $user->oxuser__oxstreetnr->value,
			'oxuser__oxzip' => $user->oxuser__oxzip->value,
			'oxuser__oxcity' => $user->oxuser__oxcity->value,
			'oxuser__oxcountryid' => $user->oxuser__oxcountryid->value,
			'oxuser__oxfon' => $user->oxuser__oxfon->value,
			'oxuser__oxmobfon' => $user->oxuser__oxmobfon->value,
			'oxuser__oxbirthdate' => $user->oxuser__oxbirthdate->value,
		);
	}

	public static function isBasketVirtual($basket)
	{
		foreach ($basket->getContents() as $oBasketItem) {
			$oProduct = $oBasketItem->getArticle(false);
			if ($oProduct->isOrderArticle()) {
				$oProduct = $oProduct->getArticle();
			}
			if (!$oProduct->oxarticles__oxnonmaterial->value) {
				return false;
			}
		}
		return true;
	}

	public static function getInvoiceItemsFromBasket($basket)
	{
		$items = array();

		$articles = $basket->getContents();
		foreach ($articles as $article) {
			$items[] = new Customweb_Payment_Authorization_DefaultInvoiceItem (
					UnzerCwHelper::toUtf8($article->getArticle()->oxarticles__oxartnum->value),
					UnzerCwHelper::toUtf8($article->getTitle()),
					$article->getPrice()->getVat(),
					$article->getPrice()->getBruttoPrice(),
					$article->getAmount(),
					Customweb_Payment_Authorization_DefaultInvoiceItem::TYPE_PRODUCT
			);
		}

		if ($basket->getCosts('oxgiftcard') && $basket->getCosts('oxgiftcard')->getBruttoPrice() > 0) {
			$items[] = new Customweb_Payment_Authorization_DefaultInvoiceItem (
					'gift_card',
					'Gift Card',
					$basket->getCosts('oxgiftcard')->getVat(),
					$basket->getCosts('oxgiftcard')->getBruttoPrice(),
					1,
					Customweb_Payment_Authorization_DefaultInvoiceItem::TYPE_PRODUCT
			);
		}

		if ($basket->getTotalDiscount() && $basket->getTotalDiscount()->getBruttoPrice() > 0) {
			$items[] = new Customweb_Payment_Authorization_DefaultInvoiceItem (
					'discount',
					'Discount',
					$basket->getTotalDiscount()->getVat(),
					$basket->getTotalDiscount()->getBruttoPrice(),
					1,
					Customweb_Payment_Authorization_DefaultInvoiceItem::TYPE_DISCOUNT
			);
		}

		/*if ($basket->getVoucherDiscount() && $basket->getVoucherDiscount()->getBruttoPrice() > 0) {
			$items[] = new Customweb_Payment_Authorization_DefaultInvoiceItem (
					'voucher_discount',
					'Voucher Discount',
					$basket->getVoucherDiscount()->getVat(),
					$basket->getVoucherDiscount()->getBruttoPrice(),
					1,
					Customweb_Payment_Authorization_DefaultInvoiceItem::TYPE_DISCOUNT
			);
		}*/

		if ($basket->getCosts('oxdelivery') && $basket->getCosts('oxdelivery')->getBruttoPrice() > 0) {
			$items[] = new Customweb_Payment_Authorization_DefaultInvoiceItem (
					'shipping',
					'Shipping',
					$basket->getCosts('oxdelivery')->getVat(),
					$basket->getCosts('oxdelivery')->getBruttoPrice(),
					1,
					Customweb_Payment_Authorization_DefaultInvoiceItem::TYPE_SHIPPING
			);
		}

		if ($basket->getCosts('oxpayment') && $basket->getCosts('oxpayment')->getBruttoPrice() > 0) {
			$items[] = new Customweb_Payment_Authorization_DefaultInvoiceItem (
					'payment_fee',
					'Payment Fee',
					$basket->getCosts('oxpayment')->getVat(),
					$basket->getCosts('oxpayment')->getBruttoPrice(),
					1,
					Customweb_Payment_Authorization_DefaultInvoiceItem::TYPE_FEE
			);
		}

		if ($basket->getCosts('oxwrapping') && $basket->getCosts('oxwrapping')->getBruttoPrice() > 0) {
			$items[] = new Customweb_Payment_Authorization_DefaultInvoiceItem (
					'wrap_fee',
					'Wrap Fee',
					$basket->getCosts('oxwrapping')->getVat(),
					$basket->getCosts('oxwrapping')->getBruttoPrice(),
					1,
					Customweb_Payment_Authorization_DefaultInvoiceItem::TYPE_FEE
			);
		}

		return Customweb_Util_Invoice::cleanupLineItems($items, $basket->getBruttoSum(), UnzerCwHelper::toUtf8($basket->getBasketCurrency()->name));
	}

	public static function getInvoiceItemsFromOrder($order)
	{
		$items = array();

		$articles = $order->getOrderArticles();
		foreach ($articles as $article) {
			$items[] = new Customweb_Payment_Authorization_DefaultInvoiceItem (
					UnzerCwHelper::toUtf8($article->oxorderarticles__oxartnum->value),
					UnzerCwHelper::toUtf8($article->oxorderarticles__oxtitle->value),
					$article->oxorderarticles__oxvat->value,
					$article->oxorderarticles__oxbrutprice->value,
					$article->oxorderarticles__oxamount->value,
					Customweb_Payment_Authorization_DefaultInvoiceItem::TYPE_PRODUCT
			);
		}

		if ($order->oxorder__oxgiftcardcost != false && $order->oxorder__oxgiftcardcost->value > 0) {
			$items[] = new Customweb_Payment_Authorization_DefaultInvoiceItem (
					'gift_card',
					'Gift Card',
					$order->oxorder__oxgiftcardvat->value,
					$order->oxorder__oxgiftcardcost->value,
					1,
					Customweb_Payment_Authorization_DefaultInvoiceItem::TYPE_PRODUCT
			);
		}

		if ($order->oxorder__oxdiscount != false && $order->oxorder__oxdiscount->value > 0) {
			$items[] = new Customweb_Payment_Authorization_DefaultInvoiceItem (
					'discount',
					'Discount',
					$order->oxorder__oxartvat1->value,
					$order->oxorder__oxdiscount->value,
					1,
					Customweb_Payment_Authorization_DefaultInvoiceItem::TYPE_DISCOUNT
			);
		}

		if ($order->oxorder__oxvoucherdiscount != false && $order->oxorder__oxvoucherdiscount->value > 0) {
			$items[] = new Customweb_Payment_Authorization_DefaultInvoiceItem (
					'voucher_discount',
					'Voucher Discount',
					$order->oxorder__oxartvat1->value,
					$order->oxorder__oxvoucherdiscount->value,
					1,
					Customweb_Payment_Authorization_DefaultInvoiceItem::TYPE_DISCOUNT
			);
		}

		if ($order->oxorder__oxdelcost != false && $order->oxorder__oxdelcost->value > 0) {
			$items[] = new Customweb_Payment_Authorization_DefaultInvoiceItem (
					'shipping',
					'Shipping',
					$order->oxorder__oxdelvat->value,
					$order->oxorder__oxdelcost->value,
					1,
					Customweb_Payment_Authorization_DefaultInvoiceItem::TYPE_SHIPPING
			);
		}

		if ($order->oxorder__oxpaycost != false && $order->oxorder__oxpaycost->value > 0) {
			$items[] = new Customweb_Payment_Authorization_DefaultInvoiceItem (
					'payment_fee',
					'Payment Fee',
					$order->oxorder__oxpayvat->value,
					$order->oxorder__oxpaycost->value,
					1,
					Customweb_Payment_Authorization_DefaultInvoiceItem::TYPE_FEE
			);
		}

		if ($order->oxorder__oxwrapcost != false && $order->oxorder__oxwrapcost->value > 0) {
			$items[] = new Customweb_Payment_Authorization_DefaultInvoiceItem (
					'wrap_fee',
					'Wrap Fee',
					$order->oxorder__oxwrapvat->value,
					$order->oxorder__oxwrapcost->value,
					1,
					Customweb_Payment_Authorization_DefaultInvoiceItem::TYPE_FEE
			);
		}

		return Customweb_Util_Invoice::cleanupLineItems($items, $order->getTotalOrderSum(), UnzerCwHelper::toUtf8($order->getOrderCurrency()->name));
	}

	private static function endsWith($haystack, $needle) {
		return $needle === '' || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
	}

	public static function isMobileTheme(){
		$isMobile = false;
		if (class_exists('oeThemeSwitcherThemeManager')) {
			$oThemeManager = new oeThemeSwitcherThemeManager();
			$isMobile = $oThemeManager->isMobileThemeRequested();
		}
		return $isMobile;
	}

	public static function canUseDatabaseTransactions() {
		$driver = self::getDriver();
		if ($driver instanceof Customweb_Database_Driver_MySQL_Driver) {
			return false;
		} else {
			return true;
		}
	}

}
