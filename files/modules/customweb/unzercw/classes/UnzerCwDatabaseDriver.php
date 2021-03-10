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

require_once 'Customweb/Database/Driver/PDO/Driver.php';


class UnzerCwDatabaseDriver extends Customweb_Database_Driver_PDO_Driver
{

	public function beginTransaction() {
		\OxidEsales\EshopCommunity\Core\DatabaseProvider::getMaster()->startTransaction();
	}

	public function commit() {
		\OxidEsales\EshopCommunity\Core\DatabaseProvider::getMaster()->commitTransaction();
	}

	public function rollBack() {
		\OxidEsales\EshopCommunity\Core\DatabaseProvider::getMaster()->rollbackTransaction();
	}

	public function isTransactionRunning() {
		return false;
	}

}