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

require_once 'Customweb/Util/Encoding.php';


/**
 * @Bean
 */
class UnzerCwAdapterIframeAdapter extends UnzerCwAdapterAbstractAdapter
{
	private $formActionUrl = null;

	public function getPaymentAdapterInterfaceName() {
		return 'Customweb_Payment_Authorization_Iframe_IAdapter';
	}

	/**
	 * @return Customweb_Payment_Authorization_Iframe_IAdapter
	 */
	public function getInterfaceAdapter() {
		return parent::getInterfaceAdapter();
	}

	protected function prepareAdapter() {
		$this->formActionUrl = UnzerCwHelper::getUrl(array(
			'cl' => 'unzercw_iframe',
			'cstrxid' => $this->getTransaction()->getTransactionId()
		));
		UnzerCwHelper::getEntityManager()->persist($this->getTransaction());
	}

	public function processOrderConfirmationRequest() {
		$vars = array(
			'formActionUrl' => $this->formActionUrl
		);
		return $vars;
	}

	public function getIframeTemplateVars() {
		$vars = array(
			'targetUrl' => $this->getInterfaceAdapter()->getIframeUrl($this->getTransaction()->getTransactionObject(), Customweb_Util_Encoding::toUTF8($_REQUEST)),
			'iframeHeight' => $this->getInterfaceAdapter()->getIframeHeight($this->getTransaction()->getTransactionObject(), Customweb_Util_Encoding::toUTF8($_REQUEST))
		);
		UnzerCwHelper::getEntityManager()->persist($this->getTransaction());
		return $vars;
	}

	protected function getFormActionUrl() {
		return $this->formActionUrl;
	}
}