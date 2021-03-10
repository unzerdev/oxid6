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



class unzercw_oxpayment extends unzercw_oxpayment_parent
{
	private $unzercwPaymentMethodWrapper = null;

	protected function getUnzercwPaymentMethodWrapper()
	{
		if ($this->unzercwPaymentMethodWrapper == null) {
			$this->unzercwPaymentMethodWrapper = new UnzerCwPaymentMethod($this->oxpayments__oxid->value);
		}
		return $this->unzercwPaymentMethodWrapper;
	}

	protected function getUnzercwAdapter()
	{
		$order = UnzerCwHelper::getOrderFromBasket();
		$adapter = UnzerCwHelper::getCheckoutAdapterByAuthorizationMethod($this->getUnzercwPaymentMethodWrapper()->getPaymentMethodConfigurationValue('authorizationMethod'));
		$adapter->prepareForm($order, $this->getUnzercwPaymentMethodWrapper());
		return $adapter;
	}

	public function isUnzercwPaymentFormOnPaymentPage()
	{
		if (false) {
			return true;
		}

		return UnzerCwHelper::isPaymentFormOnPaymentPage($this->getUnzercwPaymentMethodWrapper());
	}

	public function isUnzercwPaymentMethod()
	{
		return UnzerCwHelper::isUnzercwPaymentMethod($this->oxpayments__oxid->value);
	}

	public function getUnzercwVisibleFormFields()
	{
		if (false) {
			return '<div class="status error corners"><p>'
				. Customweb_I18n_Translation::__('We experienced a problem with your sellxed payment extension. For more information, please visit the configuration page of the plugin.')
				. '</p></div>';
		}

		if ($this->isUnzercwPaymentMethod()) {
			$visibleFormFields = $this->getUnzercwAdapter()->getVisibleFormFields();
			if ($visibleFormFields !== null && count($visibleFormFields) > 0) {
				$renderer = new UnzerCwFormRenderer();
				$renderer->setRenderOnLoadJs(false);
				$renderer->setNamespacePrefix($this->oxpayments__oxid->value);
				return UnzerCwHelper::toDefaultEncoding($renderer->renderElements($visibleFormFields));
			}
		}
	}

	public function getUnzercwAliasFormFields()
	{
		if (false) {
			return '';
		}

		if ($this->isUnzercwPaymentMethod()) {
			return $this->getUnzercwAdapter()->getAliasFormContent();
		}
	}

	public function getUnzercwAuthorizationMethod()
	{
		return $this->getUnzercwPaymentMethodWrapper()->getPaymentMethodConfigurationValue('authorizationMethod');
	}

	public function isValidPayment($aDynValue, $sShopId, $oUser, $dBasketPrice, $sShipSetId)
	{
		$result = parent::isValidPayment($aDynValue, $sShopId, $oUser, $dBasketPrice, $sShipSetId);

		if (!$this->isUnzercwPaymentMethod() || !$result) {
			return $result;
		}

		try {
			$this->getUnzercwPaymentMethodWrapper()->validateAfter($this->oxpayments__oxid->value);
			return true;
		} catch (Exception $e) {
			$this->_iPaymentError = 'unzercw';
			oxRegistry::getSession()->setVariable('payerrortext', $e->getMessage());
			return false;
		}
	}

	public function validateBefore()
	{
		if ($this->isUnzercwPaymentMethod()) {
			return $this->getUnzercwPaymentMethodWrapper()->validateBefore($this->oxpayments__oxid->value);
		}
		return parent::validateBefore();
	}
}
