<?php
/**
 *  * You are allowed to use this API in your web application.
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
 * @category Customweb
 * @package Customweb_UnzerCw
 * @version 1.0.59
 */

UnzerCwHelper::bootstrap();

require_once 'Customweb/Payment/BackendOperation/Form/IAdapter.php';
require_once 'Customweb/Form/Control/HiddenInput.php';
require_once 'Customweb/Form/HiddenElement.php';
require_once 'Customweb/IForm.php';
require_once 'Customweb/Form.php';


// Register custom translation resolver
new UnzerCwTranslationResolver();

class unzercw_backendform extends oxAdminView {

	public function render(){
		parent::render();

		try {
			$session = oxRegistry::getSession();
			$currentForm = $this->getCurrentForm();
			if ($currentForm->isProcessable()) {
				$currentForm = new Customweb_Form($currentForm);
				$currentForm->setTargetUrl($this->getViewConfig()->getSelfLink())->setRequestMethod(Customweb_IForm::REQUEST_METHOD_POST);
				if ($session->getId()) {
					$currentForm->addElement(
							new Customweb_Form_HiddenElement(new Customweb_Form_Control_HiddenInput('stoken', $session->getSessionChallengeToken())));
				}
				if ($session->isSidNeeded()) {
					$currentForm->addElement(
							new Customweb_Form_HiddenElement(new Customweb_Form_Control_HiddenInput($session->getForcedName(), $session->getId())));
				}
				$currentForm->addElement(new Customweb_Form_HiddenElement(new Customweb_Form_Control_HiddenInput('cl', 'unzercw_backendform')));
				$currentForm->addElement(new Customweb_Form_HiddenElement(new Customweb_Form_Control_HiddenInput('fnc', 'save')));
				$currentForm->addElement(
						new Customweb_Form_HiddenElement(new Customweb_Form_Control_HiddenInput('formName', $currentForm->getMachineName())));
			}

			$renderer = new UnzerCwBackendFormRenderer();
			$formHtml = $renderer->renderForm($currentForm);

			$currentForm = $this->getCurrentForm();
			$tabs = array();

			$forms = $this->getFormAdapter()->getForms();
			foreach ($forms as $form) {
				$tabs[] = array(
					'machineName' => $form->getMachineName(),
					'label' => UnzerCwHelper::toDefaultEncoding($form->getTitle()),
					'active' => ($currentForm->getMachineName() == $form->getMachineName())
				);
			}

			$this->_aViewData['tabs'] = $tabs;
			$this->_aViewData['tabLength'] = count($tabs);
			$this->_aViewData['formHtml'] = UnzerCwHelper::toDefaultEncoding($formHtml);
			$this->_aViewData['formName'] = $form->getMachineName();
		}
		catch (Exception $e) {
		}

		return "unzercw_backendform.tpl";
	}

	public function save(){
		try {
			$form = $this->getCurrentForm();

			$params = $_REQUEST;
			if (!isset($params['button'])) {
				throw new Exception("No button returned.");
			}
			$pressedButton = null;
			foreach ($params['button'] as $buttonName => $value) {
				foreach ($form->getButtons() as $button) {
					if ($button->getMachineName() == $buttonName) {
						$pressedButton = $button;
					}
				}
			}

			if ($pressedButton === null) {
				throw new Exception("Could not find pressed button.");
			}
			$this->getFormAdapter()->processForm($form, $pressedButton, $params);
		}
		catch (Exception $e) {
		}

		return 'unzercw_backendform?formName=' . $form->getMachineName();
	}

	/**
	 *
	 * @throws Exception
	 * @return Customweb_Payment_BackendOperation_Form_IAdapter
	 */
	protected function getFormAdapter(){
		$container = UnzerCwHelper::createContainer();
		$adapter = $container->getBean('Customweb_Payment_BackendOperation_Form_IAdapter');
		if (!($adapter instanceof Customweb_Payment_BackendOperation_Form_IAdapter)) {
			throw new Exception("The backend form adapter must be of type 'Customweb_Payment_BackendOperation_Form_IAdapter'");
		}
		return $adapter;
	}

	/**
	 *
	 * @throws Exception
	 * @return Customweb_Payment_BackendOperation_IForm|mixed
	 */
	protected function getCurrentForm(){
		$machineName = oxRegistry::getConfig()->getRequestParameter('formName');
		$forms = $this->getFormAdapter()->getForms();
		foreach ($forms as $form) {
			if ($form->getMachineName() == $machineName) {
				return $form;
			}
		}
		reset($forms);
		return current($forms);
	}
}