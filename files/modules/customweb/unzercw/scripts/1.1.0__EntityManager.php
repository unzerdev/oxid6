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


class Migration_Script_1_1_0 implements Customweb_Database_Migration_IScript
{
	public function execute(Customweb_Database_IDriver $driver)
	{
		$this->updateSchema($driver);
	}

	private function updateSchema(Customweb_Database_IDriver $driver)
	{
		$driver->query("ALTER TABLE  `unzercw_transaction` CHANGE  `OXID`  `oxid` VARCHAR( 32 ) NOT NULL")->execute();
		$driver->query("ALTER TABLE  `unzercw_transaction` CHANGE  `id`  `transactionId` BIGINT( 20 ) NOT NULL AUTO_INCREMENT")->execute();
		$driver->query("ALTER TABLE  `unzercw_transaction` ADD  `transactionExternalId` VARCHAR( 255 ) AFTER  `transactionId`")->execute();
		$driver->query("ALTER TABLE  `unzercw_transaction` ADD UNIQUE (`transactionExternalId`)")->execute();
		$driver->query("ALTER TABLE  `unzercw_transaction` CHANGE  `order_id`  `orderId` VARCHAR( 255 )")->execute();
		$driver->query("ALTER TABLE  `unzercw_transaction` CHANGE  `alias_for_display`  `aliasForDisplay` VARCHAR( 255 )")->execute();
		$driver->query("ALTER TABLE  `unzercw_transaction` CHANGE  `alias_active`  `aliasActive` CHAR( 1 )")->execute();
		$driver->query("UPDATE  `unzercw_transaction` SET  `aliasActive` = 'y' WHERE `aliasActive` = 1")->execute();
		$driver->query("UPDATE  `unzercw_transaction` SET  `aliasActive` = 'n' WHERE `aliasActive` = 0")->execute();
		$driver->query("ALTER TABLE  `unzercw_transaction` CHANGE  `payment_method`  `paymentMachineName` VARCHAR( 255 )")->execute();
		$driver->query("ALTER TABLE  `unzercw_transaction` CHANGE  `transaction_object`  `transactionObject` LONGTEXT")->execute();
		$driver->query("ALTER TABLE  `unzercw_transaction` CHANGE  `authorization_type`  `authorizationType` VARCHAR( 255 )")->execute();
		$driver->query("ALTER TABLE  `unzercw_transaction` CHANGE  `user_id`  `customerId` VARCHAR( 255 )")->execute();
		$driver->query("ALTER TABLE  `unzercw_transaction` CHANGE  `updated_at`  `updatedOn` DATETIME")->execute();
		$driver->query("ALTER TABLE  `unzercw_transaction` CHANGE  `created_at`  `createdOn` DATETIME")->execute();
		$driver->query("ALTER TABLE  `unzercw_transaction` CHANGE  `payment_id`  `paymentId` VARCHAR( 255 )")->execute();
		$driver->query("ALTER TABLE  `unzercw_transaction` CHANGE  `updatable`  `updatable` CHAR( 1 )")->execute();
		$driver->query("UPDATE  `unzercw_transaction` SET  `updatable` = 'y' WHERE `updatable` = 1")->execute();
		$driver->query("UPDATE  `unzercw_transaction` SET  `updatable` = 'n' WHERE `updatable` = 0")->execute();
		$driver->query("ALTER TABLE  `unzercw_transaction` ADD  `executeUpdateOn` DATETIME AFTER  `updatable`")->execute();
		$driver->query("ALTER TABLE  `unzercw_transaction` ADD  `authorizationAmount` DECIMAL( 20, 5 ) AFTER  `executeUpdateOn`")->execute();
		$driver->query("ALTER TABLE  `unzercw_transaction` ADD  `authorizationStatus` VARCHAR( 255 ) AFTER  `authorizationAmount`")->execute();
		$driver->query("ALTER TABLE  `unzercw_transaction` ADD  `paid` CHAR( 1 ) AFTER  `authorizationStatus`")->execute();
		$driver->query("ALTER TABLE  `unzercw_transaction` ADD  `currency` VARCHAR( 255 ) AFTER  `paid`")->execute();
		$driver->query("ALTER TABLE  `unzercw_transaction` CHANGE  `session_data`  `sessionData` LONGTEXT")->execute();
		$driver->query("ALTER TABLE  `unzercw_transaction` CHANGE  `shop_id`  `shopId` VARCHAR( 32 )")->execute();
		$driver->query("ALTER TABLE  `unzercw_transaction` ADD KEY  `transactionExternalId_orderId_paymentId` (  `transactionExternalId` ,  `orderId` ,  `paymentId` )")->execute();
		$driver->query("ALTER TABLE  `unzercw_customer_context` DROP `OXID`")->execute();
		$driver->query("ALTER TABLE  `unzercw_customer_context` CHANGE  `user_id`  `customerId` VARCHAR( 255 )")->execute();
		$driver->query("ALTER TABLE  `unzercw_customer_context` DROP PRIMARY KEY")->execute();
		$driver->query("ALTER TABLE  `unzercw_customer_context` ADD  `contextId` BIGINT( 20 ) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST")->execute();
		$driver->query("ALTER TABLE  `unzercw_customer_context` CHANGE  `customer_context`  `context_values` LONGTEXT")->execute();
		$driver->query("ALTER TABLE  `unzercw_customer_context` DROP  `created_at`")->execute();
		$driver->query("ALTER TABLE  `unzercw_customer_context` DROP  `updated_at`")->execute();
		$driver->query("ALTER TABLE  `unzercw_customer_context` ADD UNIQUE KEY  `customerId` (  `customerId` )")->execute();
	}
}