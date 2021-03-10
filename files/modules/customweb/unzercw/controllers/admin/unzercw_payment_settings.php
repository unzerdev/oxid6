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



class Unzercw_Payment_Settings extends Shop_Config
{
	protected $_confParams = array(
		'confstrs'			=> 'str',
		'confselects'		=> 'select',
		'conftextareas' 	=> 'textarea',
		'confpasswords'		=> 'password',
		'confmultiselects'	=> 'multiselect',
		'confmultilangs'    => 'multilang',
		'conffiles'			=> 'file'
	);

	public function render()
	{
		parent::render();

		$paymentId = $this->_aViewData["oxid"] = $this->getEditObjectId();

		if (UnzerCwHelper::isUnzercwPaymentMethod($paymentId)) {
			$this->populateViewDataWithConfigVars($this->getConfig()->getShopId());

			$this->_aViewData['paymentSettings'] = UnzerCwPaymentMethodSetting::getConfigForm($paymentId);

			$this->_aViewData['languages'] = $this->getConfig()->getConfigParam('aLanguages');

			return 'unzercw_payment_settings.tpl';
		}

		return 'unzercw_payment_settings_invalid.tpl';
	}

	/**
	 * Saves shop configuration variables
	 *
	 * @return null
	 */
	public function saveConfVars()
	{
		$myConfig = $this->getConfig();

		foreach ($this->_confParams as $param => $varType) {
			$values = oxRegistry::getConfig()->getRequestParameter($param);

			if (is_array($values)) {
				foreach ($values as $varName => $varValue)
				{
					switch ($varType) {
						case 'multiselect':
						case 'arr':
							unset($varValue['dummy']);
							foreach ($varValue as $key => $value) {
								$varValue[$key] = utf8_encode($value);
							}
// 							$varValue = implode(',', $varValue);
							$varType = 'arr';
							break;
						case 'multilang':
							foreach ($varValue as $key => $value) {
								$varValue[$key] = utf8_encode($value);
							}
							$varValue = serialize($varValue);
							break;
						default:
							$varValue = utf8_encode($varValue);
							break;
					}
					$myConfig->saveShopConfVar($varType, $varName, $varValue, null, 'module:unzercw');
				}
			}
		}
	}

	protected function populateViewDataWithConfigVars($shopId)
	{
		$aDbVariables = $this->loadConfVars($shopId, $moduleName   = '');
		$aConfVars    = $aDbVariables['vars'];

		foreach ($this->_aConfParams as $sType => $sParam)
		{
			$varValue = $aConfVars[$sType];
			foreach ($varValue as $key => $value) {
				if (is_array($value)) {
					foreach ($value as $k => $v) {
						$varValue[$key][$k] = utf8_decode($v);
					}
				} else {
					$varValue[$key] = utf8_decode($value);
				}
			}
			$this->_aViewData[$sParam] = $varValue;
		}
	}
}