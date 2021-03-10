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

require_once 'Customweb/Form/Renderer.php';


class UnzerCwFormRenderer extends Customweb_Form_Renderer
{
	public function getElementCssClass()
	{
		return parent::getElementCssClass() . ' form-group';
	}

	public function getElementLabelCssClass()
	{
		return parent::getElementLabelCssClass() . ' req col-sm-3';
	}

	public function renderElementLabel(Customweb_Form_IElement $element)
	{
		return parent::renderElementLabel($element) . '<div class="col-sm-9">';
	}

	public function renderElementPostfix(Customweb_Form_IElement $element)
	{
		return '</div>' . parent::renderElementPostfix($element);
	}
}