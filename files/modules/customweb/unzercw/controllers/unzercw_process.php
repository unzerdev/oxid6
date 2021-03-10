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

require_once 'Customweb/Util/Encoding.php';
require_once 'Customweb/Core/Http/Response.php';


class unzercw_process extends oxUBase
{
	public function render()
	{
		$dispatcher = UnzerCwHelper::getEndpointDispatcher();
		$response = $dispatcher->invokeControllerAction(UnzerCwContextRequest::getInstance(), 'process', 'index');
		$wrapper = new Customweb_Core_Http_Response($response);
		$wrapper->send();
		die();
	}

	public function authorize()
	{
		$transaction = UnzerCwHelper::loadTransaction(oxRegistry::getConfig()->getRequestParameter('cstrxid'));
		$adapter = UnzerCwHelper::getAuthorizationAdapter($transaction->getAuthorizationType());

		$transactionObject = $transaction->getTransactionObject();
		$response = $adapter->processAuthorization($transactionObject, Customweb_Util_Encoding::toUTF8($_REQUEST));
		UnzerCwHelper::createContainer()->getBean('Customweb_Payment_ITransactionHandler')->persistTransactionObject($transactionObject);

		$wrapper = new Customweb_Core_Http_Response($response);
		$wrapper->send();
		die();
	}

	public function success()
	{
		$sameSiteFix = oxRegistry::getConfig()->getRequestParameter('s');
		if (empty($sameSiteFix)) {
			header_remove('Set-Cookie');
			header('Location: ' . UnzerCwHelper::getUrl(array(
				'cl' => 'unzercw_process',
				'fnc' => 'success',
				'cstrxid' => oxRegistry::getConfig()->getRequestParameter('cstrxid'),
				's' => 1
			)));
			die();
		} else {
			$redirectionUrl = UnzerCwHelper::waitForNotification(oxRegistry::getConfig()->getRequestParameter('cstrxid'));

			header("Location: " . $redirectionUrl);
			die();
		}
	}

	public function fail()
	{
		$sameSiteFix = oxRegistry::getConfig()->getRequestParameter('s');
		if (empty($sameSiteFix)) {
			header_remove('Set-Cookie');
			header('Location: ' . UnzerCwHelper::getUrl(array(
				'cl' => 'unzercw_process',
				'fnc' => 'fail',
				'cstrxid' => oxRegistry::getConfig()->getRequestParameter('cstrxid'),
				's' => 1
			)));
			die();
		} else {
			$transaction = UnzerCwHelper::loadTransaction(oxRegistry::getConfig()->getRequestParameter('cstrxid'));

			$errorMessages = $transaction->getTransactionObject()->getErrorMessages();
			if (is_array($errorMessages) && !empty($errorMessages)) {
				$messageToDisplay = nl2br((string) end($errorMessages));
				reset($errorMessages);
				oxRegistry::get("oxUtilsView")->addErrorToDisplay(UnzerCwHelper::toDefaultEncoding($messageToDisplay));
			}

			$user = $this->getUser();
			if (!$user || $transaction->getOrder()->oxorder__oxuserid->value != $user->getId()) {
				header("Location: " . UnzerCwHelper::getUrl(array(
					'cl' => 'user'
				)));
				die();
			}

			if (UnzerCwHelper::isCreateOrderBefore() && $transaction->getOrder() instanceof oxOrder) {
				if (UnzerCwHelper::isDeleteOrderOnFailedAuthorization()) {
					$transaction->getOrder()->delete();
				} else {
					$transaction->getOrder()->setPaymentFailedStatus();
					oxRegistry::getSession()->deleteVariable( 'sess_challenge' );
				}
			}

			if (UnzerCwHelper::isPaymentFormOnPaymentPage($transaction->getTransactionObject()->getTransactionContext()->getOrderContext()->getPaymentMethod())) {
				$redirectionUrl = UnzerCwHelper::getUrl(array(
					'cl' => 'payment',
				));
			} else {
				$redirectionUrl = UnzerCwHelper::getUrl(array(
					'cl' => 'order',
				));
			}

			header("Location: " . $redirectionUrl);
			die();
		}
	}
}