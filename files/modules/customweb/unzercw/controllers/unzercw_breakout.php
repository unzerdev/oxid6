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



class unzercw_breakout extends oxUBase
{
	protected $_sThisTemplate = 'unzercw_breakout.tpl';

	/**
	 * Display the breakout page required by iframe authorization method.
	 */
	public function render()
	{
		parent::render();

		$transaction = UnzerCwHelper::loadTransaction(oxRegistry::getConfig()->getRequestParameter('cstrxid'));

		$redirectionUrl = '';
		$transactionContext = $transaction->getTransactionObject()->getTransactionContext();
		if ($transactionContext->getTransaction()->getTransactionObject()->isAuthorizationFailed()) {
			$redirectionUrl = UnzerCwHelper::getUrl($transactionContext->getCustomParameters(), $transactionContext->getFailedUrl());
		} else {
			$redirectionUrl = UnzerCwHelper::getUrl($transactionContext->getCustomParameters(), $transactionContext->getSuccessUrl());
		}

		$this->_aViewData['breakoutUrl'] = $redirectionUrl;

		return $this->_sThisTemplate;
	}
}