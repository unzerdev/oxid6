CREATE TABLE IF NOT EXISTS unzercw_external_checkout_context (
	OXID char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
	contextId bigint(20) NOT NULL AUTO_INCREMENT,
	state varchar (255) ,
	failedErrorMessage varchar (255) ,
	cartUrl varchar (255) ,
	defaultCheckoutUrl varchar (255) ,
	invoiceItems LONGTEXT ,
	orderAmountInDecimals decimal (20,5) ,
	currencyCode varchar (255) ,
	languageCode varchar (255) ,
	customerEmailAddress varchar (255) ,
	customerId varchar (255) ,
	transactionId int (11) ,
	shippingAddress LONGTEXT ,
	billingAddress LONGTEXT ,
	shippingMethodName varchar (255) ,
	paymentMethodMachineName varchar (255) ,
	providerData LONGTEXT ,
	createdOn datetime ,
	updatedOn datetime ,
	securityToken varchar (255) ,
	securityTokenExpiryDate datetime NULL DEFAULT NULL,
	authenticationSuccessUrl varchar(512) NULL DEFAULT NULL,
	authenticationEmailAddress varchar (255) NULL DEFAULT NULL,
	PRIMARY KEY (contextId)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;