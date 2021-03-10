ALTER TABLE `unzercw_transaction` ADD `versionNumber` int NOT NULL;
ALTER TABLE `unzercw_transaction` ADD `liveTransaction` char(1);
ALTER TABLE `unzercw_customer_context` ADD `versionNumber` int NOT NULL;
ALTER TABLE `unzercw_external_checkout_context` ADD `versionNumber` int NOT NULL;