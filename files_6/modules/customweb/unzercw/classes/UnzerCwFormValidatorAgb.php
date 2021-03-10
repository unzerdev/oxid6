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

require_once 'Customweb/Form/Validator/IValidator.php';
require_once 'Customweb/Form/Validator/Abstract.php';


class UnzerCwFormValidatorAgb extends Customweb_Form_Validator_Abstract implements Customweb_Form_Validator_IValidator
{
	/**
	 * (non-PHPdoc)
	 * @see Customweb_Form_Validator_Abstract::getValidationCondition()
	 */
	public function getValidationCondition() {
		return 'element.value == "1"';
	}
}