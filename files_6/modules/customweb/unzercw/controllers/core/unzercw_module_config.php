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



class unzercw_module_config extends unzercw_module_config_parent
{
	public function render()
	{
		$sModuleId = $this->getEditObjectId();
		if($sModuleId == 'unzercw'){
			$this->_aConfParams['textarea'] = 'conftextareas';
			$this->_aConfParams['cwpassword'] = 'confpasswords';
			$this->_aConfParams['multilang'] = 'confmultilangs';
			$this->_aConfParams['multiselect'] = 'confmultiselects';
			$this->_aConfParams['file'] = 'conffiles';
			$this->_aConfParams['cwselect'] = 'confselects';
		}

		$this->_aViewData['languages'] = $this->getConfig()->getConfigParam('aLanguages');

		return parent::render();
	}

	protected function _parseConstraint($sType, $sConstraint)
	{
		switch ($sType) {
			case "cwselect":
			case "multiselect":
				return array_map('trim', explode('|', $sConstraint));
			case "file":
				return UnzerCwHelper::getFileOptions($sConstraint);
		}
		return parent::_parseConstraint($sType, $sConstraint);
	}

	public function _unserializeConfVar($sType, $sName, $sValue)
	{
		if (strpos($sName, 'unzercw_') === 0) {
			switch ($sType) {
				case "cwpassword":
				case "textarea":
				case "str":
				case "cwselect":
					$sValue = utf8_decode($sValue);
					break;
			}
		}

		$oStr = getStr();
		$mData = null;

		switch ($sType) {
			case "file":
			case "cwpassword":
			case "textarea":
			case "cwselect":
				$mData = $oStr->htmlentities($sValue);
				break;
			case "multilang":
				$mData = unserialize($sValue);
				if (!is_array($mData)) {
					$defaultValue = $mData;
					$mData = array();
					foreach (array_keys($this->getConfig()->getConfigParam('aLanguages')) as $langKey) {
						$mData[$langKey] = $defaultValue;
					}
				}
				foreach ($mData as $key => $value) {
					if (strpos($sName, 'unzercw_') === 0) {
						$value = utf8_decode($value);
					}
					$mData[$key] = $oStr->htmlentities($value);
				}
				break;
			case "multiselect":
				if (empty($sValue)) {
					$mData = array();
				} else {
					$mData = explode(',', $sValue);
				}
				break;
			default:
				$mData = parent::_unserializeConfVar($sType, $sName, $sValue);
				break;
		}

		return $mData;
	}

	public function _serializeConfVar($sType, $sName, $mValue)
	{
		if (strpos($sName, 'unzercw_') === 0) {
			switch ($sType) {
				case "cwpassword":
				case "textarea":
				case "str":
				case "cwselect":
					$mValue = utf8_encode($mValue);
					break;
				case "multilang":
					foreach ($mValue as $key => $value) {
						$mValue[$key] = utf8_encode($value);
					}
					break;
			}
		}

		$sData = $mValue;

		switch ($sType) {
			case "file":
				break;
			case "cwpassword":
			case "textarea":
				break;
			case "multilang":
				$sData = serialize($mValue);
				break;
			case "multiselect":
				if (is_array($mValue)) {
					unset($mValue['dummy']);
					$sData = implode(',', $mValue);
				}
				break;
			default:
				$sData = parent::_serializeConfVar($sType, $sName, $mValue);
				break;
		}

		return $sData;
	}

	public function saveConfVars()
	{
		$sModuleId = $this->getEditObjectId();
		if($sModuleId == 'unzercw'){
			$this->_aConfParams['textarea'] = 'conftextareas';
			$this->_aConfParams['cwpassword'] = 'confpasswords';
			$this->_aConfParams['multilang'] = 'confmultilangs';
			$this->_aConfParams['multiselect'] = 'confmultiselects';
			$this->_aConfParams['file'] = 'conffiles';
			$this->_aConfParams['cwselect'] = 'confselects';
		}

		return parent::saveConfVars();
	}
}