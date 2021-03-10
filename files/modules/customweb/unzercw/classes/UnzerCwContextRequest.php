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

require_once 'Customweb/Core/Http/ContextRequest.php';
require_once 'Customweb/Util/Encoding.php';


class UnzerCwContextRequest extends Customweb_Core_Http_ContextRequest
{
	private static $instance = null;

	/**
	 * @return UnzerCwContextRequest
	 */
	public static function getInstance() {
		if (self::$instance === null) {
			self::$instance =  new UnzerCwContextRequest();
		}
		return self::$instance;
	}

	public function getParsedQuery() {
		return Customweb_Util_Encoding::toUTF8(parent::getParsedQuery());
	}

	public function getQuery() {
		return Customweb_Util_Encoding::toUTF8(parent::getQuery());
	}

	public function getParsedBody() {
		return Customweb_Util_Encoding::toUTF8(parent::getParsedBody());
	}

	public function getBody() {
		return Customweb_Util_Encoding::toUTF8(parent::getBody());
	}

	public function getParameters() {
		return Customweb_Util_Encoding::toUTF8(parent::getParameters());
	}

	public function getCookies() {
		return Customweb_Util_Encoding::toUTF8(parent::getCookies());
	}
}