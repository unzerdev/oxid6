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

require_once 'Customweb/Database/Migration/IScript.php';


class UnzerCw_Migration_1_3_3 implements Customweb_Database_Migration_IScript {

	public function execute(Customweb_Database_IDriver $driver){
		$result = $driver->query("SHOW COLUMNS FROM `unzercw_transaction` LIKE 'orderNumber'");
		if ($result->getRowCount() <= 0) {
			$driver->query("ALTER TABLE `unzercw_transaction` ADD `orderNumber` INT(11)")->execute();

		}

		$result = $driver->query("SHOW COLUMNS FROM `unzercw_transaction` LIKE 'paymentType'");
		if ($result->getRowCount() <= 0) {
			$driver->query("ALTER TABLE `unzercw_transaction` ADD `paymentType` CHAR(32)")->execute();
		}

		$driver->query("UPDATE unzercw_transaction, oxorder
				SET unzercw_transaction.orderNumber = oxorder.oxordernr, unzercw_transaction.paymentType = oxorder.oxpaymenttype
				WHERE unzercw_transaction.orderId = oxorder.oxid AND unzercw_transaction.orderNumber IS NULL;")->execute();
	}
}