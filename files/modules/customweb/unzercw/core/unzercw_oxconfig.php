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

class unzercw_oxconfig extends unzercw_oxconfig_parent
{
	public function decodeValue($sType, $mOrigValue)
	{
		$sValue = $mOrigValue;
		switch ($sType) {
			case "multiselect":
				if (empty($mOrigValue)) {
					$sValue = array();
				} else if (is_string($mOrigValue)) {
					$sValue = explode(',', $mOrigValue);
				} else {
					parent::decodeValue($sType, $mOrigValue);
				}
				break;
			case "multilang":
				$sValue = unserialize($mOrigValue);
				break;
			default:
				$sValue = parent::decodeValue($sType, $mOrigValue);
				break;
		}
		return $sValue;
	}
}