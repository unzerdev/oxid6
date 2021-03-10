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



class UnzerCwPreparation
{
	public static function alterVarLengths()
	{
		try {
			$rowCount = (int) oxDb::getDb()->getOne("SELECT COUNT(*) FROM `unzercw_schema_version`");
		} catch (Exception $e) {
			$rowCount = 0;
		}
		if ($rowCount == 0) {
			$queries = array(
				"ALTER TABLE `oxconfig` CHANGE `OXVARNAME` `OXVARNAME` CHAR(64) NOT NULL DEFAULT '';",
				"ALTER TABLE `oxpayments` CHANGE `OXID` `OXID` CHAR(64) NOT NULL COMMENT 'Payment id';",
				"ALTER TABLE `oxorder` CHANGE `OXPAYMENTTYPE` `OXPAYMENTTYPE` CHAR(64) NOT NULL DEFAULT  '' COMMENT  'Payment id (oxpayments)';",
				"ALTER TABLE `oxobject2payment` CHANGE `OXPAYMENTID` `OXPAYMENTID` CHAR(64) NOT NULL DEFAULT  '' COMMENT  'Payment id (oxpayments)';",
				"ALTER TABLE `oxobject2group` CHANGE `OXOBJECTID` `OXOBJECTID` CHAR(64) NOT NULL DEFAULT  '' COMMENT  'User id'"
			);
			foreach ($queries as $query) {
				oxDb::getDb()->Execute($query);
			}
		}
	}
}