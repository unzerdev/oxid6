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
require_once 'Customweb/Licensing/UnzerCw/License.php';



class unzercw_navigation extends unzercw_navigation_parent
{
	protected function _doStartUpChecks()
	{
		$aMessage = parent::_doStartUpChecks();

		if (false) {
			$reason = Customweb_Licensing_UnzerCw_License::getValidationErrorMessage();
			if ($reason === null) {
				$reason = 'Unknown error.';
			}
			$token = Customweb_Licensing_UnzerCw_License::getCurrentToken();
			$licenseWarning = 'Unzer: ' . Customweb_I18n_Translation::__('There is a problem with your license. Please contact us (www.sellxed.com/support). Reason: !reason Current Token: !token', array('!reason' => $reason, '!token' => $token));
			$aMessage['warning'] .= ((! empty($aMessage['warning']))?"<br><br>":'') . $licenseWarning;
		}

		return $aMessage;
	}
}
