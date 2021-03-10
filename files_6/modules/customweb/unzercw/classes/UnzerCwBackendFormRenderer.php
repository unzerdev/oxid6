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

require_once 'Customweb/Form/Control/IEditableControl.php';
require_once 'Customweb/Form/HiddenElement.php';
require_once 'Customweb/Form/Renderer.php';


class UnzerCwBackendFormRenderer extends Customweb_Form_Renderer
{
	public function getDescriptionCssClass() {
		return 'note';
	}

	public function getErrorMessageCssClass() {
		return 'oxValidateError';
	}

	public function renderElementGroupPrefix(Customweb_Form_IElementGroup $elementGroup)
	{
		return '<div class="groupExp"><div class="exp">';
	}

	public function renderElementGroupPostfix(Customweb_Form_IElementGroup $elementGroup)
	{
		return '</div></div>';
	}

	public function renderElementGroupTitle(Customweb_Form_IElementGroup $elementGroup)
	{
		$output = '';
		$title = $elementGroup->getTitle();
		if (! empty($title)) {
			$cssClass = $this->getCssClassPrefix() . $this->getElementGroupTitleCssClass();
			$output .= '<a class="rc ' . $cssClass . '"><b>' . $title . '</b></a>';
		}
		return $output;
	}

	public function renderElementPrefix(Customweb_Form_IElement $element) {
		$classes = $this->getCssClassPrefix() . $this->getElementCssClass();
		$classes .= ' ' . $this->getCssClassPrefix() . $element->getElementIntention()->getCssClass();

		$errorMessage = $element->getErrorMessage();
		if (!empty($errorMessage)) {
			$classes .= ' ' . $this->getCssClassPrefix() . $this->getElementErrorCssClass();
		}

		if ($element instanceof Customweb_Form_HiddenElement) {
			return '';
		} else {
			return '<dl class="' . $classes . '" id="' . $element->getElementId() . '"><dt style="margin-right: 10px;">';
		}
	}

	public function renderElementPostfix(Customweb_Form_IElement $element) {
		return '</dd><div class="spacer"></div></dl>';
	}

	public function renderElementLabel(Customweb_Form_IElement $element)
	{
		return '';
	}

	protected function renderLabel($referenceTo, $label, $class)
	{
		$for = '';
		if (!empty($referenceTo)) {
			$for = ' for="' . $referenceTo . '" ';
		}
		return '<label class="' . $class . '" ' . $for . ' style="float: left;">' . $label . '</label>';
	}

	public function renderElementAdditional(Customweb_Form_IElement $element)
	{
		if ($element instanceof Customweb_Form_HiddenElement) {
			$output = '';
		} else {
			$output = '</dt><dd>';
		}

		if (method_exists($element, 'getLabel')) {
			$output .= parent::renderElementLabel($element);
		}

		if ($element->getControl() instanceof Customweb_Form_Control_IEditableControl) {
			$output .= ($element->isGlobalScope() ? '<span style="float: left; margin-left: 5px; color: #777;">[GLOBAL]</span>' : '');
		}

		$errorMessage = $element->getErrorMessage();
		if (!empty($errorMessage)) {
			$output .= $this->renderElementErrorMessage($element);
		}

		// if (!$element->isGlobalScope()) {
		// 	$output .= $this->renderElementScope($element);
		// }

		$description = $element->getDescription();
		if (!empty($description)) {
			$output .= $this->renderElementDescription($element);
		}

		return $output;
	}

	protected function renderElementDescription(Customweb_Form_IElement $element)
	{
		$descriptionId = $element->getControl()->getControlId() . '-desc';
		$output = '';
		$output .= '<input type="button" id="helpBtn_' . $descriptionId . '" class="btnShowHelpPanel" onclick="YAHOO.oxid.help.showPanel(\'' . $descriptionId . '\');" style="margin-left: 10px;" />';
		$output .= '<div id="helpText_' . $descriptionId . '" class="helpPanelText">' . $element->getDescription() . '</div>';
		return $output;
	}

	public function renderControlPrefix(Customweb_Form_Control_IControl $control, $controlTypeClass) {
		return '<div class="control-element">';
	}

	public function renderControlPostfix(Customweb_Form_Control_IControl $control, $controlTypeClass) {
		return '</div>';
	}


	protected function renderButtons(array $buttons, $jsFunctionPostfix = '')
	{
		$output = '<br/>';
		foreach ($buttons as $button) {
			$output .= $this->renderButton($button, $jsFunctionPostfix);
		}
		return $output;
	}
}