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

require_once 'Customweb/Form/Control/MultiControl.php';
require_once 'Customweb/Form/IControlCssClassResolver.php';
require_once 'Customweb/Form/Validator/NotEmpty.php';


class UnzerCwControlCssClassResolver implements Customweb_Form_IControlCssClassResolver
{
	public function resolveClass(Customweb_Form_Control_IControl $control, Customweb_Form_IElement $element)
	{
		$css = '';

		if (!($control instanceof Customweb_Form_Control_MultiControl)) {
			$css .= ' form-control';
		}

		if (!($element->getControl() instanceof Customweb_Form_Control_MultiControl)) {
			$validators = $element->getValidators();
			if (count($validators) > 0) {
				$css .= ' js-oxValidate';
			}
			foreach ($validators as $validator) {
				if ($validator instanceof Customweb_Form_Validator_NotEmpty && !strstr($css, 'js-oxValidate_notEmpty')) {
					$css .= ' js-oxValidate_notEmpty';
				}
			}
		}

		return $css;
	}
}
