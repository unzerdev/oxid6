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



class unzercw_payment_list extends unzercw_payment_list_parent
{
	public function render()
	{
		parent::render();

		$this->_aViewData['enabledModule'] = false;
		$this->_aViewData['noSelection'] = false;

		$paymentId = $this->getEditObjectId();
		if ($paymentId != '-1' && isset($paymentId)) {
            if (UnzerCwHelper::isUnzercwPaymentMethod($paymentId)) {
            	$this->_aViewData['enabledModule'] = 'unzercw';
            }
		} else {
			$this->_aViewData['noSelection'] = true;
		}

		return "unzercw_payment_list.tpl";
	}
}