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


class Migration_Script_1_0_0 implements Customweb_Database_Migration_IScript
{
	public function execute(Customweb_Database_IDriver $driver)
	{
		$this->installTransactionTable($driver);
		$this->installCustomerContextTable($driver);
		$this->installStorageTable($driver);
		$this->update($driver);

		UnzerCwHelper::cleanupTransactions();
	}

	private function getConfig()
	{
		return oxRegistry::getConfig();
	}

	private function installTransactionTable(Customweb_Database_IDriver $driver)
	{
		$driver->query("CREATE TABLE IF NOT EXISTS `unzercw_transaction` (
			`OXID` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`order_id` char(32) NOT NULL default '',
			`alias_for_display` varchar(255) DEFAULT NULL,
			`alias_active` tinyint(1) NOT NULL,
			`payment_method` varchar(255) NOT NULL,
			`payment_id` char(32) NOT NULL,
			`transaction_object` longtext NOT NULL,
			`authorization_type` varchar(255) NOT NULL,
			`user_id` char(32) NOT NULL default '',
			`created_at` datetime NOT NULL,
			`updated_at` datetime NOT NULL,
			`updatable` tinyint(1) NOT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8;")->execute();
	}

	private function installCustomerContextTable(Customweb_Database_IDriver $driver)
	{
		$driver->query("CREATE TABLE IF NOT EXISTS `unzercw_customer_context` (
			`OXID` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
			`user_id` char(32) NOT NULL default '',
			`customer_context` longtext NOT NULL,
			`created_at` datetime NOT NULL,
			`updated_at` datetime NOT NULL,
			PRIMARY KEY (`user_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8;")->execute();
	}

	private function installStorageTable(Customweb_Database_IDriver $driver)
	{
		$driver->query("CREATE TABLE IF NOT EXISTS `unzercw_storage` (
			`keyId` bigint(20) NOT NULL AUTO_INCREMENT,
			`keyName` varchar(165) DEFAULT NULL,
			`keySpace` varchar(165) DEFAULT NULL,
			`keyValue` longtext,
			PRIMARY KEY (`keyId`),
			UNIQUE KEY `keyName_keySpace` (`keyName`,`keySpace`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8;")->execute();
	}

	private function update(Customweb_Database_IDriver $driver)
	{
		try {
			$driver->query('ALTER TABLE `unzercw_transaction` ADD  `session_data` longtext NOT NULL')->execute();
		} catch (Exception $e) {}

		try {
			$driver->query('ALTER TABLE `unzercw_transaction` ADD  `shop_id` char(32) NOT NULL')->execute();
			$driver->query('UPDATE `unzercw_transaction` AS t, `oxorder` AS o SET t.`shop_id` = o.`OXSHOPID` WHERE t.`order_id` = o.`OXID`')->execute();
		} catch (Exception $e) {}
	}
}