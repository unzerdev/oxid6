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


class unzercw_pending extends oxUBase
{
	protected $_sThisTemplate = 'unzercw_pending.tpl';

	/**
	 * Display the pending notification page.
	 */
	public function render()
	{
		parent::render();

		try {
			$transaction = UnzerCwHelper::loadTransaction(oxRegistry::getConfig()->getRequestParameter('cstrxid'));

			$redirectionUrl = UnzerCwHelper::handleTransactionStatus($transaction->getTransactionId());
			if ($redirectionUrl !== false) {
				header('Location: ' . $redirectionUrl);
				die();
			}

			$this->_aViewData['checkoutUrl'] = UnzerCwHelper::getUrl(array(
				'cl' => 'order'
			));
			$this->_aViewData['checkoutButtonLabel'] = Customweb_I18n_Translation::__('Checkout');
			$this->_aViewData['checkAgainButtonLabel'] = Customweb_I18n_Translation::__('Check Again');
			$this->_aViewData['pendingTitle'] = Customweb_I18n_Translation::__('The status of your payment is unclear.');
			$this->_aViewData['pendingText'] = Customweb_I18n_Translation::__('It is possible, that the payment has been successfully processed. Please contact us so we can check.');
			$this->_aViewData['checkAgainText'] = Customweb_I18n_Translation::__("The payment status might be updated timely. To check if there is an update, click on 'Check Again'.");

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

	public function getBreadCrumb()
	{
		$aPaths = array();
		$aPath = array();

		$iLang = oxRegistry::getLang()->getBaseLanguage();
		$aPath['title'] = 'Zahlungsstatus unklar';
		$aPath['link']  = UnzerCwHelper::getUrl(array(
			'cl' => 'unzercw_pending',
			'cstrxid' => oxRegistry::getConfig()->getRequestParameter('cstrxid')
		));
		$aPaths[] = $aPath;

		return $aPaths;
	}
}