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

require_once 'Customweb/Core/Stream/Input/File.php';


class UnzerCwPaymentMethodSetting
{
	protected static $_configFormCache = array();

	protected static function getConfigElements($paymentId)
	{
		if ($paymentId === 'unzercw_creditcard') {
			return array(
				array(
					'name' => 'placeholder_size',
 					'options' => array(
						'wide' => 'Wide (label from
							Unzer)
						',
 						'narrow' => 'Narrow (label from shop)',
 					),
 					'description' => 'How should elements fromUnzer be loaded? With narrow elementsthe element label is displayed by the store, with wide elements itisloaded via javascript by Unzer. Theinput elements are always loaded fromUnzer.',
 					'type' => 'select',
 					'title' => 'Element Size',
 					'value' => 'narrow',
 					'required' => 1,
 				),
				array(
					'name' => 'send_basket',
 					'options' => array(
						'no' => 'Do not send',
 						'yes' => 'Send Basket',
 					),
 					'description' => 'Should the invoice items be transmitted toUnzer? This slightly increases theprocessing time due to an additional request, and may cause issuesfor certain quantity / price combinations.',
 					'type' => 'select',
 					'title' => 'Send Basket',
 					'value' => 'no',
 					'required' => 1,
 				),
				array(
					'name' => 'capturing',
 					'options' => array(
						'direct' => 'Direct Charge',
 						'deferred' => 'Authorize',
 					),
 					'description' => 'Should the amount be captured automatically after theorder (Direct Charge) or should the amount only be reserved (Authorize)?',
 					'type' => 'select',
 					'title' => 'Capturing',
 					'value' => 'direct',
 					'required' => 1,
 				),
				array(
					'name' => 'status_authorized',
 					'options' => self::getOrderFolders(array(
					)),
 					'description' => 'This status is set, when the payment was successfulland it is authorized.',
 					'type' => 'select',
 					'title' => 'Authorized Status',
 					'value' => 'ORDERFOLDER_NEW',
 					'required' => 1,
 				),
				array(
					'name' => 'status_uncertain',
 					'options' => self::getOrderFolders(array(
					)),
 					'description' => 'You can specify the order status for new orders thathave an uncertain authorisation status.',
 					'type' => 'select',
 					'title' => 'Uncertain Status',
 					'value' => 'ORDERFOLDER_PROBLEMS',
 					'required' => 1,
 				),
				array(
					'name' => 'status_cancelled',
 					'options' => self::getOrderFolders(array(
						'no_status_change' => 'Don\'t change order status',
 					)),
 					'description' => 'You can specify the order status when an order iscancelled.',
 					'type' => 'select',
 					'title' => 'Cancelled Status',
 					'value' => 'ORDERFOLDER_FINISHED',
 					'required' => 1,
 				),
				array(
					'name' => 'status_captured',
 					'options' => self::getOrderFolders(array(
						'no_status_change' => 'Don\'t change order status',
 					)),
 					'description' => 'You can specify the order status for orders that arecaptured either directly after the order or manually in thebackend.',
 					'type' => 'select',
 					'title' => 'Captured Status',
 					'value' => 'no_status_change',
 					'required' => 1,
 				),
				array(
					'name' => 'authorizationMethod',
 					'options' => array(
						'AjaxAuthorization' => 'Ajax Authorization',
 					),
 					'description' => 'Select the authorization method to use for processing this payment method.',
 					'type' => 'select',
 					'title' => 'Authorization Method',
 					'value' => 'AjaxAuthorization',
 					'required' => 1,
 				),
				array(
					'name' => 'min_order_total',
 					'description' => 'This payment method is only available in case the order total is equal to or greater than the specified amount.',
 					'type' => 'str',
 					'title' => 'Minimal Order Total',
 					'value' => '',
 					'required' => 0,
 				),
				array(
					'name' => 'max_order_total',
 					'description' => 'This payment method is only available in case the order total is equal to or less than the specified amount.',
 					'type' => 'str',
 					'title' => 'Maximal Order Total',
 					'value' => '',
 					'required' => 0,
 				),
				array(
					'name' => 'order_status',
 					'options' => self::getOrderFolders(),
 					'description' => 'You can decide on the order status new orders should have that have an uncertain authorization status.',
 					'type' => 'select',
 					'title' => 'New Order Status',
 					'value' => '0',
 					'required' => 1,
 				),
				array(
					'name' => 'form_position',
 					'options' => array('payment' => 'On payment selection page', 'checkout' => 'On checkout page', 'separate' => 'On separate page'),
 					'description' => 'Decide where the payment form should be displayed.',
 					'type' => 'select',
 					'title' => 'Payment Form Position',
 					'value' => 'checkout',
 					'required' => 1,
 				),
			);
		}
		if ($paymentId === 'unzercw_directdebitssepa') {
			return array(
				array(
					'name' => 'status_authorized',
 					'options' => self::getOrderFolders(array(
					)),
 					'description' => 'This status is set, when the payment was successfulland it is authorized.',
 					'type' => 'select',
 					'title' => 'Authorized Status',
 					'value' => 'ORDERFOLDER_NEW',
 					'required' => 1,
 				),
				array(
					'name' => 'status_uncertain',
 					'options' => self::getOrderFolders(array(
					)),
 					'description' => 'You can specify the order status for new orders thathave an uncertain authorisation status.',
 					'type' => 'select',
 					'title' => 'Uncertain Status',
 					'value' => 'ORDERFOLDER_PROBLEMS',
 					'required' => 1,
 				),
				array(
					'name' => 'status_cancelled',
 					'options' => self::getOrderFolders(array(
						'no_status_change' => 'Don\'t change order status',
 					)),
 					'description' => 'You can specify the order status when an order iscancelled.',
 					'type' => 'select',
 					'title' => 'Cancelled Status',
 					'value' => 'ORDERFOLDER_FINISHED',
 					'required' => 1,
 				),
				array(
					'name' => 'status_captured',
 					'options' => self::getOrderFolders(array(
						'no_status_change' => 'Don\'t change order status',
 					)),
 					'description' => 'You can specify the order status for orders that arecaptured either directly after the order or manually in thebackend.',
 					'type' => 'select',
 					'title' => 'Captured Status',
 					'value' => 'no_status_change',
 					'required' => 1,
 				),
				array(
					'name' => 'placeholder_size',
 					'options' => array(
						'wide' => 'Wide (label from
							Unzer)
						',
 						'narrow' => 'Narrow (label from shop)',
 					),
 					'description' => 'How should elements fromUnzer be loaded? With narrow elementsthe element label is displayed by the store, with wide elements itisloaded via javascript by Unzer. Theinput elements are always loaded fromUnzer.',
 					'type' => 'select',
 					'title' => 'Element Size',
 					'value' => 'narrow',
 					'required' => 1,
 				),
				array(
					'name' => 'send_basket',
 					'options' => array(
						'no' => 'Do not send',
 						'yes' => 'Send Basket',
 					),
 					'description' => 'Should the invoice items be transmitted toUnzer? This slightly increases theprocessing time due to an additional request, and may cause issuesfor certain quantity / price combinations.',
 					'type' => 'select',
 					'title' => 'Send Basket',
 					'value' => 'no',
 					'required' => 1,
 				),
				array(
					'name' => 'send_customer',
 					'options' => array(
						'no' => 'Do not send',
 						'yes' => 'Send Customer',
 					),
 					'description' => 'Should customer data be transmitted toUnzer? This slightly increases theprocessing time due to an additional request, but may allow e.g.saving the payment method to the customer.',
 					'type' => 'select',
 					'title' => 'Send Customer',
 					'value' => 'no',
 					'required' => 1,
 				),
				array(
					'name' => 'merchant_name',
 					'description' => 'Here you can configure the merchant name which isdisplayed as part of the mandate text.',
 					'type' => 'str',
 					'title' => 'Merchant name',
 					'value' => '',
 					'required' => 1,
 				),
				array(
					'name' => 'authorizationMethod',
 					'options' => array(
						'AjaxAuthorization' => 'Ajax Authorization',
 					),
 					'description' => 'Select the authorization method to use for processing this payment method.',
 					'type' => 'select',
 					'title' => 'Authorization Method',
 					'value' => 'AjaxAuthorization',
 					'required' => 1,
 				),
				array(
					'name' => 'min_order_total',
 					'description' => 'This payment method is only available in case the order total is equal to or greater than the specified amount.',
 					'type' => 'str',
 					'title' => 'Minimal Order Total',
 					'value' => '',
 					'required' => 0,
 				),
				array(
					'name' => 'max_order_total',
 					'description' => 'This payment method is only available in case the order total is equal to or less than the specified amount.',
 					'type' => 'str',
 					'title' => 'Maximal Order Total',
 					'value' => '',
 					'required' => 0,
 				),
				array(
					'name' => 'order_status',
 					'options' => self::getOrderFolders(),
 					'description' => 'You can decide on the order status new orders should have that have an uncertain authorization status.',
 					'type' => 'select',
 					'title' => 'New Order Status',
 					'value' => '0',
 					'required' => 1,
 				),
				array(
					'name' => 'form_position',
 					'options' => array('payment' => 'On payment selection page', 'checkout' => 'On checkout page', 'separate' => 'On separate page'),
 					'description' => 'Decide where the payment form should be displayed.',
 					'type' => 'select',
 					'title' => 'Payment Form Position',
 					'value' => 'checkout',
 					'required' => 1,
 				),
			);
		}
		if ($paymentId === 'unzercw_securesepa') {
			return array(
				array(
					'name' => 'merchant_name',
 					'description' => 'Here you can configure the merchant name which isdisplayed as part of the mandate text.',
 					'type' => 'str',
 					'title' => 'Merchant name',
 					'value' => '',
 					'required' => 1,
 				),
				array(
					'name' => 'status_authorized',
 					'options' => self::getOrderFolders(array(
					)),
 					'description' => 'This status is set, when the payment was successfulland it is authorized.',
 					'type' => 'select',
 					'title' => 'Authorized Status',
 					'value' => 'ORDERFOLDER_NEW',
 					'required' => 1,
 				),
				array(
					'name' => 'status_uncertain',
 					'options' => self::getOrderFolders(array(
					)),
 					'description' => 'You can specify the order status for new orders thathave an uncertain authorisation status.',
 					'type' => 'select',
 					'title' => 'Uncertain Status',
 					'value' => 'ORDERFOLDER_PROBLEMS',
 					'required' => 1,
 				),
				array(
					'name' => 'status_cancelled',
 					'options' => self::getOrderFolders(array(
						'no_status_change' => 'Don\'t change order status',
 					)),
 					'description' => 'You can specify the order status when an order iscancelled.',
 					'type' => 'select',
 					'title' => 'Cancelled Status',
 					'value' => 'ORDERFOLDER_FINISHED',
 					'required' => 1,
 				),
				array(
					'name' => 'status_captured',
 					'options' => self::getOrderFolders(array(
						'no_status_change' => 'Don\'t change order status',
 					)),
 					'description' => 'You can specify the order status for orders that arecaptured either directly after the order or manually in thebackend.',
 					'type' => 'select',
 					'title' => 'Captured Status',
 					'value' => 'no_status_change',
 					'required' => 1,
 				),
				array(
					'name' => 'placeholder_size',
 					'options' => array(
						'wide' => 'Wide (label from
							Unzer)
						',
 						'narrow' => 'Narrow (label from shop)',
 					),
 					'description' => 'How should elements fromUnzer be loaded? With narrow elementsthe element label is displayed by the store, with wide elements itisloaded via javascript by Unzer. Theinput elements are always loaded fromUnzer.',
 					'type' => 'select',
 					'title' => 'Element Size',
 					'value' => 'narrow',
 					'required' => 1,
 				),
				array(
					'name' => 'authorizationMethod',
 					'options' => array(
						'AjaxAuthorization' => 'Ajax Authorization',
 					),
 					'description' => 'Select the authorization method to use for processing this payment method.',
 					'type' => 'select',
 					'title' => 'Authorization Method',
 					'value' => 'AjaxAuthorization',
 					'required' => 1,
 				),
				array(
					'name' => 'min_order_total',
 					'description' => 'This payment method is only available in case the order total is equal to or greater than the specified amount.',
 					'type' => 'str',
 					'title' => 'Minimal Order Total',
 					'value' => '',
 					'required' => 0,
 				),
				array(
					'name' => 'max_order_total',
 					'description' => 'This payment method is only available in case the order total is equal to or less than the specified amount.',
 					'type' => 'str',
 					'title' => 'Maximal Order Total',
 					'value' => '',
 					'required' => 0,
 				),
				array(
					'name' => 'order_status',
 					'options' => self::getOrderFolders(),
 					'description' => 'You can decide on the order status new orders should have that have an uncertain authorization status.',
 					'type' => 'select',
 					'title' => 'New Order Status',
 					'value' => '0',
 					'required' => 1,
 				),
				array(
					'name' => 'form_position',
 					'options' => array('payment' => 'On payment selection page', 'checkout' => 'On checkout page', 'separate' => 'On separate page'),
 					'description' => 'Decide where the payment form should be displayed.',
 					'type' => 'select',
 					'title' => 'Payment Form Position',
 					'value' => 'checkout',
 					'required' => 1,
 				),
			);
		}
		if ($paymentId === 'unzercw_openinvoice') {
			return array(
				array(
					'name' => 'send_customer',
 					'options' => array(
						'no' => 'Do not send',
 						'yes' => 'Send Customer',
 					),
 					'description' => 'Should customer data be transmitted toUnzer? This slightly increases theprocessing time due to an additional request, but may allow e.g.saving the payment method to the customer.',
 					'type' => 'select',
 					'title' => 'Send Customer',
 					'value' => 'no',
 					'required' => 1,
 				),
				array(
					'name' => 'status_authorized',
 					'options' => self::getOrderFolders(array(
					)),
 					'description' => 'This status is set, when the payment was successfulland it is authorized.',
 					'type' => 'select',
 					'title' => 'Authorized Status',
 					'value' => 'ORDERFOLDER_NEW',
 					'required' => 1,
 				),
				array(
					'name' => 'status_uncertain',
 					'options' => self::getOrderFolders(array(
					)),
 					'description' => 'You can specify the order status for new orders thathave an uncertain authorisation status.',
 					'type' => 'select',
 					'title' => 'Uncertain Status',
 					'value' => 'ORDERFOLDER_PROBLEMS',
 					'required' => 1,
 				),
				array(
					'name' => 'status_cancelled',
 					'options' => self::getOrderFolders(array(
						'no_status_change' => 'Don\'t change order status',
 					)),
 					'description' => 'You can specify the order status when an order iscancelled.',
 					'type' => 'select',
 					'title' => 'Cancelled Status',
 					'value' => 'ORDERFOLDER_FINISHED',
 					'required' => 1,
 				),
				array(
					'name' => 'status_captured',
 					'options' => self::getOrderFolders(array(
						'no_status_change' => 'Don\'t change order status',
 					)),
 					'description' => 'You can specify the order status for orders that arecaptured either directly after the order or manually in thebackend.',
 					'type' => 'select',
 					'title' => 'Captured Status',
 					'value' => 'no_status_change',
 					'required' => 1,
 				),
				array(
					'name' => 'send_basket',
 					'options' => array(
						'no' => 'Do not send',
 						'yes' => 'Send Basket',
 					),
 					'description' => 'Should the invoice items be transmitted toUnzer? This slightly increases theprocessing time due to an additional request, and may cause issuesfor certain quantity / price combinations.',
 					'type' => 'select',
 					'title' => 'Send Basket',
 					'value' => 'no',
 					'required' => 1,
 				),
				array(
					'name' => 'authorizationMethod',
 					'options' => array(
						'AjaxAuthorization' => 'Ajax Authorization',
 					),
 					'description' => 'Select the authorization method to use for processing this payment method.',
 					'type' => 'select',
 					'title' => 'Authorization Method',
 					'value' => 'AjaxAuthorization',
 					'required' => 1,
 				),
				array(
					'name' => 'min_order_total',
 					'description' => 'This payment method is only available in case the order total is equal to or greater than the specified amount.',
 					'type' => 'str',
 					'title' => 'Minimal Order Total',
 					'value' => '',
 					'required' => 0,
 				),
				array(
					'name' => 'max_order_total',
 					'description' => 'This payment method is only available in case the order total is equal to or less than the specified amount.',
 					'type' => 'str',
 					'title' => 'Maximal Order Total',
 					'value' => '',
 					'required' => 0,
 				),
				array(
					'name' => 'order_status',
 					'options' => self::getOrderFolders(),
 					'description' => 'You can decide on the order status new orders should have that have an uncertain authorization status.',
 					'type' => 'select',
 					'title' => 'New Order Status',
 					'value' => '0',
 					'required' => 1,
 				),
				array(
					'name' => 'form_position',
 					'options' => array('payment' => 'On payment selection page', 'checkout' => 'On checkout page', 'separate' => 'On separate page'),
 					'description' => 'Decide where the payment form should be displayed.',
 					'type' => 'select',
 					'title' => 'Payment Form Position',
 					'value' => 'checkout',
 					'required' => 1,
 				),
			);
		}
		if ($paymentId === 'unzercw_secureinvoice') {
			return array(
				array(
					'name' => 'status_authorized',
 					'options' => self::getOrderFolders(array(
					)),
 					'description' => 'This status is set, when the payment was successfulland it is authorized.',
 					'type' => 'select',
 					'title' => 'Authorized Status',
 					'value' => 'ORDERFOLDER_NEW',
 					'required' => 1,
 				),
				array(
					'name' => 'status_uncertain',
 					'options' => self::getOrderFolders(array(
					)),
 					'description' => 'You can specify the order status for new orders thathave an uncertain authorisation status.',
 					'type' => 'select',
 					'title' => 'Uncertain Status',
 					'value' => 'ORDERFOLDER_PROBLEMS',
 					'required' => 1,
 				),
				array(
					'name' => 'status_cancelled',
 					'options' => self::getOrderFolders(array(
						'no_status_change' => 'Don\'t change order status',
 					)),
 					'description' => 'You can specify the order status when an order iscancelled.',
 					'type' => 'select',
 					'title' => 'Cancelled Status',
 					'value' => 'ORDERFOLDER_FINISHED',
 					'required' => 1,
 				),
				array(
					'name' => 'status_captured',
 					'options' => self::getOrderFolders(array(
						'no_status_change' => 'Don\'t change order status',
 					)),
 					'description' => 'You can specify the order status for orders that arecaptured either directly after the order or manually in thebackend.',
 					'type' => 'select',
 					'title' => 'Captured Status',
 					'value' => 'no_status_change',
 					'required' => 1,
 				),
				array(
					'name' => 'authorizationMethod',
 					'options' => array(
						'AjaxAuthorization' => 'Ajax Authorization',
 					),
 					'description' => 'Select the authorization method to use for processing this payment method.',
 					'type' => 'select',
 					'title' => 'Authorization Method',
 					'value' => 'AjaxAuthorization',
 					'required' => 1,
 				),
				array(
					'name' => 'min_order_total',
 					'description' => 'This payment method is only available in case the order total is equal to or greater than the specified amount.',
 					'type' => 'str',
 					'title' => 'Minimal Order Total',
 					'value' => '',
 					'required' => 0,
 				),
				array(
					'name' => 'max_order_total',
 					'description' => 'This payment method is only available in case the order total is equal to or less than the specified amount.',
 					'type' => 'str',
 					'title' => 'Maximal Order Total',
 					'value' => '',
 					'required' => 0,
 				),
				array(
					'name' => 'order_status',
 					'options' => self::getOrderFolders(),
 					'description' => 'You can decide on the order status new orders should have that have an uncertain authorization status.',
 					'type' => 'select',
 					'title' => 'New Order Status',
 					'value' => '0',
 					'required' => 1,
 				),
				array(
					'name' => 'form_position',
 					'options' => array('payment' => 'On payment selection page', 'checkout' => 'On checkout page', 'separate' => 'On separate page'),
 					'description' => 'Decide where the payment form should be displayed.',
 					'type' => 'select',
 					'title' => 'Payment Form Position',
 					'value' => 'checkout',
 					'required' => 1,
 				),
			);
		}
		if ($paymentId === 'unzercw_paypal') {
			return array(
				array(
					'name' => 'status_authorized',
 					'options' => self::getOrderFolders(array(
					)),
 					'description' => 'This status is set, when the payment was successfulland it is authorized.',
 					'type' => 'select',
 					'title' => 'Authorized Status',
 					'value' => 'ORDERFOLDER_NEW',
 					'required' => 1,
 				),
				array(
					'name' => 'status_uncertain',
 					'options' => self::getOrderFolders(array(
					)),
 					'description' => 'You can specify the order status for new orders thathave an uncertain authorisation status.',
 					'type' => 'select',
 					'title' => 'Uncertain Status',
 					'value' => 'ORDERFOLDER_PROBLEMS',
 					'required' => 1,
 				),
				array(
					'name' => 'status_cancelled',
 					'options' => self::getOrderFolders(array(
						'no_status_change' => 'Don\'t change order status',
 					)),
 					'description' => 'You can specify the order status when an order iscancelled.',
 					'type' => 'select',
 					'title' => 'Cancelled Status',
 					'value' => 'ORDERFOLDER_FINISHED',
 					'required' => 1,
 				),
				array(
					'name' => 'status_captured',
 					'options' => self::getOrderFolders(array(
						'no_status_change' => 'Don\'t change order status',
 					)),
 					'description' => 'You can specify the order status for orders that arecaptured either directly after the order or manually in thebackend.',
 					'type' => 'select',
 					'title' => 'Captured Status',
 					'value' => 'no_status_change',
 					'required' => 1,
 				),
				array(
					'name' => 'send_basket',
 					'options' => array(
						'no' => 'Do not send',
 						'yes' => 'Send Basket',
 					),
 					'description' => 'Should the invoice items be transmitted toUnzer? This slightly increases theprocessing time due to an additional request, and may cause issuesfor certain quantity / price combinations.',
 					'type' => 'select',
 					'title' => 'Send Basket',
 					'value' => 'no',
 					'required' => 1,
 				),
				array(
					'name' => 'send_customer',
 					'options' => array(
						'no' => 'Do not send',
 						'yes' => 'Send Customer',
 					),
 					'description' => 'Should customer data be transmitted toUnzer? This slightly increases theprocessing time due to an additional request, but may allow e.g.saving the payment method to the customer.',
 					'type' => 'select',
 					'title' => 'Send Customer',
 					'value' => 'no',
 					'required' => 1,
 				),
				array(
					'name' => 'capturing',
 					'options' => array(
						'direct' => 'Direct Charge',
 						'deferred' => 'Authorize',
 					),
 					'description' => 'Should the amount be captured automatically after theorder (Direct Charge) or should the amount only be reserved (Authorize)?',
 					'type' => 'select',
 					'title' => 'Capturing',
 					'value' => 'direct',
 					'required' => 1,
 				),
				array(
					'name' => 'authorizationMethod',
 					'options' => array(
						'AjaxAuthorization' => 'Ajax Authorization',
 					),
 					'description' => 'Select the authorization method to use for processing this payment method.',
 					'type' => 'select',
 					'title' => 'Authorization Method',
 					'value' => 'AjaxAuthorization',
 					'required' => 1,
 				),
				array(
					'name' => 'min_order_total',
 					'description' => 'This payment method is only available in case the order total is equal to or greater than the specified amount.',
 					'type' => 'str',
 					'title' => 'Minimal Order Total',
 					'value' => '',
 					'required' => 0,
 				),
				array(
					'name' => 'max_order_total',
 					'description' => 'This payment method is only available in case the order total is equal to or less than the specified amount.',
 					'type' => 'str',
 					'title' => 'Maximal Order Total',
 					'value' => '',
 					'required' => 0,
 				),
				array(
					'name' => 'order_status',
 					'options' => self::getOrderFolders(),
 					'description' => 'You can decide on the order status new orders should have that have an uncertain authorization status.',
 					'type' => 'select',
 					'title' => 'New Order Status',
 					'value' => '0',
 					'required' => 1,
 				),
				array(
					'name' => 'form_position',
 					'options' => array('payment' => 'On payment selection page', 'checkout' => 'On checkout page', 'separate' => 'On separate page'),
 					'description' => 'Decide where the payment form should be displayed.',
 					'type' => 'select',
 					'title' => 'Payment Form Position',
 					'value' => 'checkout',
 					'required' => 1,
 				),
			);
		}
		if ($paymentId === 'unzercw_sofortueberweisung') {
			return array(
				array(
					'name' => 'status_authorized',
 					'options' => self::getOrderFolders(array(
					)),
 					'description' => 'This status is set, when the payment was successfulland it is authorized.',
 					'type' => 'select',
 					'title' => 'Authorized Status',
 					'value' => 'ORDERFOLDER_NEW',
 					'required' => 1,
 				),
				array(
					'name' => 'status_uncertain',
 					'options' => self::getOrderFolders(array(
					)),
 					'description' => 'You can specify the order status for new orders thathave an uncertain authorisation status.',
 					'type' => 'select',
 					'title' => 'Uncertain Status',
 					'value' => 'ORDERFOLDER_PROBLEMS',
 					'required' => 1,
 				),
				array(
					'name' => 'status_cancelled',
 					'options' => self::getOrderFolders(array(
						'no_status_change' => 'Don\'t change order status',
 					)),
 					'description' => 'You can specify the order status when an order iscancelled.',
 					'type' => 'select',
 					'title' => 'Cancelled Status',
 					'value' => 'ORDERFOLDER_FINISHED',
 					'required' => 1,
 				),
				array(
					'name' => 'status_captured',
 					'options' => self::getOrderFolders(array(
						'no_status_change' => 'Don\'t change order status',
 					)),
 					'description' => 'You can specify the order status for orders that arecaptured either directly after the order or manually in thebackend.',
 					'type' => 'select',
 					'title' => 'Captured Status',
 					'value' => 'no_status_change',
 					'required' => 1,
 				),
				array(
					'name' => 'send_basket',
 					'options' => array(
						'no' => 'Do not send',
 						'yes' => 'Send Basket',
 					),
 					'description' => 'Should the invoice items be transmitted toUnzer? This slightly increases theprocessing time due to an additional request, and may cause issuesfor certain quantity / price combinations.',
 					'type' => 'select',
 					'title' => 'Send Basket',
 					'value' => 'no',
 					'required' => 1,
 				),
				array(
					'name' => 'send_customer',
 					'options' => array(
						'no' => 'Do not send',
 						'yes' => 'Send Customer',
 					),
 					'description' => 'Should customer data be transmitted toUnzer? This slightly increases theprocessing time due to an additional request, but may allow e.g.saving the payment method to the customer.',
 					'type' => 'select',
 					'title' => 'Send Customer',
 					'value' => 'no',
 					'required' => 1,
 				),
				array(
					'name' => 'authorizationMethod',
 					'options' => array(
						'AjaxAuthorization' => 'Ajax Authorization',
 					),
 					'description' => 'Select the authorization method to use for processing this payment method.',
 					'type' => 'select',
 					'title' => 'Authorization Method',
 					'value' => 'AjaxAuthorization',
 					'required' => 1,
 				),
				array(
					'name' => 'min_order_total',
 					'description' => 'This payment method is only available in case the order total is equal to or greater than the specified amount.',
 					'type' => 'str',
 					'title' => 'Minimal Order Total',
 					'value' => '',
 					'required' => 0,
 				),
				array(
					'name' => 'max_order_total',
 					'description' => 'This payment method is only available in case the order total is equal to or less than the specified amount.',
 					'type' => 'str',
 					'title' => 'Maximal Order Total',
 					'value' => '',
 					'required' => 0,
 				),
				array(
					'name' => 'order_status',
 					'options' => self::getOrderFolders(),
 					'description' => 'You can decide on the order status new orders should have that have an uncertain authorization status.',
 					'type' => 'select',
 					'title' => 'New Order Status',
 					'value' => '0',
 					'required' => 1,
 				),
				array(
					'name' => 'form_position',
 					'options' => array('payment' => 'On payment selection page', 'checkout' => 'On checkout page', 'separate' => 'On separate page'),
 					'description' => 'Decide where the payment form should be displayed.',
 					'type' => 'select',
 					'title' => 'Payment Form Position',
 					'value' => 'checkout',
 					'required' => 1,
 				),
			);
		}
		if ($paymentId === 'unzercw_giropay') {
			return array(
				array(
					'name' => 'status_authorized',
 					'options' => self::getOrderFolders(array(
					)),
 					'description' => 'This status is set, when the payment was successfulland it is authorized.',
 					'type' => 'select',
 					'title' => 'Authorized Status',
 					'value' => 'ORDERFOLDER_NEW',
 					'required' => 1,
 				),
				array(
					'name' => 'status_uncertain',
 					'options' => self::getOrderFolders(array(
					)),
 					'description' => 'You can specify the order status for new orders thathave an uncertain authorisation status.',
 					'type' => 'select',
 					'title' => 'Uncertain Status',
 					'value' => 'ORDERFOLDER_PROBLEMS',
 					'required' => 1,
 				),
				array(
					'name' => 'status_cancelled',
 					'options' => self::getOrderFolders(array(
						'no_status_change' => 'Don\'t change order status',
 					)),
 					'description' => 'You can specify the order status when an order iscancelled.',
 					'type' => 'select',
 					'title' => 'Cancelled Status',
 					'value' => 'ORDERFOLDER_FINISHED',
 					'required' => 1,
 				),
				array(
					'name' => 'status_captured',
 					'options' => self::getOrderFolders(array(
						'no_status_change' => 'Don\'t change order status',
 					)),
 					'description' => 'You can specify the order status for orders that arecaptured either directly after the order or manually in thebackend.',
 					'type' => 'select',
 					'title' => 'Captured Status',
 					'value' => 'no_status_change',
 					'required' => 1,
 				),
				array(
					'name' => 'send_basket',
 					'options' => array(
						'no' => 'Do not send',
 						'yes' => 'Send Basket',
 					),
 					'description' => 'Should the invoice items be transmitted toUnzer? This slightly increases theprocessing time due to an additional request, and may cause issuesfor certain quantity / price combinations.',
 					'type' => 'select',
 					'title' => 'Send Basket',
 					'value' => 'no',
 					'required' => 1,
 				),
				array(
					'name' => 'send_customer',
 					'options' => array(
						'no' => 'Do not send',
 						'yes' => 'Send Customer',
 					),
 					'description' => 'Should customer data be transmitted toUnzer? This slightly increases theprocessing time due to an additional request, but may allow e.g.saving the payment method to the customer.',
 					'type' => 'select',
 					'title' => 'Send Customer',
 					'value' => 'no',
 					'required' => 1,
 				),
				array(
					'name' => 'authorizationMethod',
 					'options' => array(
						'AjaxAuthorization' => 'Ajax Authorization',
 					),
 					'description' => 'Select the authorization method to use for processing this payment method.',
 					'type' => 'select',
 					'title' => 'Authorization Method',
 					'value' => 'AjaxAuthorization',
 					'required' => 1,
 				),
				array(
					'name' => 'min_order_total',
 					'description' => 'This payment method is only available in case the order total is equal to or greater than the specified amount.',
 					'type' => 'str',
 					'title' => 'Minimal Order Total',
 					'value' => '',
 					'required' => 0,
 				),
				array(
					'name' => 'max_order_total',
 					'description' => 'This payment method is only available in case the order total is equal to or less than the specified amount.',
 					'type' => 'str',
 					'title' => 'Maximal Order Total',
 					'value' => '',
 					'required' => 0,
 				),
				array(
					'name' => 'order_status',
 					'options' => self::getOrderFolders(),
 					'description' => 'You can decide on the order status new orders should have that have an uncertain authorization status.',
 					'type' => 'select',
 					'title' => 'New Order Status',
 					'value' => '0',
 					'required' => 1,
 				),
				array(
					'name' => 'form_position',
 					'options' => array('payment' => 'On payment selection page', 'checkout' => 'On checkout page', 'separate' => 'On separate page'),
 					'description' => 'Decide where the payment form should be displayed.',
 					'type' => 'select',
 					'title' => 'Payment Form Position',
 					'value' => 'checkout',
 					'required' => 1,
 				),
			);
		}
		if ($paymentId === 'unzercw_przelewy24') {
			return array(
				array(
					'name' => 'status_authorized',
 					'options' => self::getOrderFolders(array(
					)),
 					'description' => 'This status is set, when the payment was successfulland it is authorized.',
 					'type' => 'select',
 					'title' => 'Authorized Status',
 					'value' => 'ORDERFOLDER_NEW',
 					'required' => 1,
 				),
				array(
					'name' => 'status_uncertain',
 					'options' => self::getOrderFolders(array(
					)),
 					'description' => 'You can specify the order status for new orders thathave an uncertain authorisation status.',
 					'type' => 'select',
 					'title' => 'Uncertain Status',
 					'value' => 'ORDERFOLDER_PROBLEMS',
 					'required' => 1,
 				),
				array(
					'name' => 'status_cancelled',
 					'options' => self::getOrderFolders(array(
						'no_status_change' => 'Don\'t change order status',
 					)),
 					'description' => 'You can specify the order status when an order iscancelled.',
 					'type' => 'select',
 					'title' => 'Cancelled Status',
 					'value' => 'ORDERFOLDER_FINISHED',
 					'required' => 1,
 				),
				array(
					'name' => 'status_captured',
 					'options' => self::getOrderFolders(array(
						'no_status_change' => 'Don\'t change order status',
 					)),
 					'description' => 'You can specify the order status for orders that arecaptured either directly after the order or manually in thebackend.',
 					'type' => 'select',
 					'title' => 'Captured Status',
 					'value' => 'no_status_change',
 					'required' => 1,
 				),
				array(
					'name' => 'send_basket',
 					'options' => array(
						'no' => 'Do not send',
 						'yes' => 'Send Basket',
 					),
 					'description' => 'Should the invoice items be transmitted toUnzer? This slightly increases theprocessing time due to an additional request, and may cause issuesfor certain quantity / price combinations.',
 					'type' => 'select',
 					'title' => 'Send Basket',
 					'value' => 'no',
 					'required' => 1,
 				),
				array(
					'name' => 'send_customer',
 					'options' => array(
						'no' => 'Do not send',
 						'yes' => 'Send Customer',
 					),
 					'description' => 'Should customer data be transmitted toUnzer? This slightly increases theprocessing time due to an additional request, but may allow e.g.saving the payment method to the customer.',
 					'type' => 'select',
 					'title' => 'Send Customer',
 					'value' => 'no',
 					'required' => 1,
 				),
				array(
					'name' => 'authorizationMethod',
 					'options' => array(
						'AjaxAuthorization' => 'Ajax Authorization',
 					),
 					'description' => 'Select the authorization method to use for processing this payment method.',
 					'type' => 'select',
 					'title' => 'Authorization Method',
 					'value' => 'AjaxAuthorization',
 					'required' => 1,
 				),
				array(
					'name' => 'min_order_total',
 					'description' => 'This payment method is only available in case the order total is equal to or greater than the specified amount.',
 					'type' => 'str',
 					'title' => 'Minimal Order Total',
 					'value' => '',
 					'required' => 0,
 				),
				array(
					'name' => 'max_order_total',
 					'description' => 'This payment method is only available in case the order total is equal to or less than the specified amount.',
 					'type' => 'str',
 					'title' => 'Maximal Order Total',
 					'value' => '',
 					'required' => 0,
 				),
				array(
					'name' => 'order_status',
 					'options' => self::getOrderFolders(),
 					'description' => 'You can decide on the order status new orders should have that have an uncertain authorization status.',
 					'type' => 'select',
 					'title' => 'New Order Status',
 					'value' => '0',
 					'required' => 1,
 				),
				array(
					'name' => 'form_position',
 					'options' => array('payment' => 'On payment selection page', 'checkout' => 'On checkout page', 'separate' => 'On separate page'),
 					'description' => 'Decide where the payment form should be displayed.',
 					'type' => 'select',
 					'title' => 'Payment Form Position',
 					'value' => 'checkout',
 					'required' => 1,
 				),
			);
		}
		if ($paymentId === 'unzercw_ideal') {
			return array(
				array(
					'name' => 'status_authorized',
 					'options' => self::getOrderFolders(array(
					)),
 					'description' => 'This status is set, when the payment was successfulland it is authorized.',
 					'type' => 'select',
 					'title' => 'Authorized Status',
 					'value' => 'ORDERFOLDER_NEW',
 					'required' => 1,
 				),
				array(
					'name' => 'status_uncertain',
 					'options' => self::getOrderFolders(array(
					)),
 					'description' => 'You can specify the order status for new orders thathave an uncertain authorisation status.',
 					'type' => 'select',
 					'title' => 'Uncertain Status',
 					'value' => 'ORDERFOLDER_PROBLEMS',
 					'required' => 1,
 				),
				array(
					'name' => 'status_cancelled',
 					'options' => self::getOrderFolders(array(
						'no_status_change' => 'Don\'t change order status',
 					)),
 					'description' => 'You can specify the order status when an order iscancelled.',
 					'type' => 'select',
 					'title' => 'Cancelled Status',
 					'value' => 'ORDERFOLDER_FINISHED',
 					'required' => 1,
 				),
				array(
					'name' => 'status_captured',
 					'options' => self::getOrderFolders(array(
						'no_status_change' => 'Don\'t change order status',
 					)),
 					'description' => 'You can specify the order status for orders that arecaptured either directly after the order or manually in thebackend.',
 					'type' => 'select',
 					'title' => 'Captured Status',
 					'value' => 'no_status_change',
 					'required' => 1,
 				),
				array(
					'name' => 'placeholder_size',
 					'options' => array(
						'wide' => 'Wide (label from
							Unzer)
						',
 						'narrow' => 'Narrow (label from shop)',
 					),
 					'description' => 'How should elements fromUnzer be loaded? With narrow elementsthe element label is displayed by the store, with wide elements itisloaded via javascript by Unzer. Theinput elements are always loaded fromUnzer.',
 					'type' => 'select',
 					'title' => 'Element Size',
 					'value' => 'narrow',
 					'required' => 1,
 				),
				array(
					'name' => 'send_basket',
 					'options' => array(
						'no' => 'Do not send',
 						'yes' => 'Send Basket',
 					),
 					'description' => 'Should the invoice items be transmitted toUnzer? This slightly increases theprocessing time due to an additional request, and may cause issuesfor certain quantity / price combinations.',
 					'type' => 'select',
 					'title' => 'Send Basket',
 					'value' => 'no',
 					'required' => 1,
 				),
				array(
					'name' => 'send_customer',
 					'options' => array(
						'no' => 'Do not send',
 						'yes' => 'Send Customer',
 					),
 					'description' => 'Should customer data be transmitted toUnzer? This slightly increases theprocessing time due to an additional request, but may allow e.g.saving the payment method to the customer.',
 					'type' => 'select',
 					'title' => 'Send Customer',
 					'value' => 'no',
 					'required' => 1,
 				),
				array(
					'name' => 'authorizationMethod',
 					'options' => array(
						'AjaxAuthorization' => 'Ajax Authorization',
 					),
 					'description' => 'Select the authorization method to use for processing this payment method.',
 					'type' => 'select',
 					'title' => 'Authorization Method',
 					'value' => 'AjaxAuthorization',
 					'required' => 1,
 				),
				array(
					'name' => 'min_order_total',
 					'description' => 'This payment method is only available in case the order total is equal to or greater than the specified amount.',
 					'type' => 'str',
 					'title' => 'Minimal Order Total',
 					'value' => '',
 					'required' => 0,
 				),
				array(
					'name' => 'max_order_total',
 					'description' => 'This payment method is only available in case the order total is equal to or less than the specified amount.',
 					'type' => 'str',
 					'title' => 'Maximal Order Total',
 					'value' => '',
 					'required' => 0,
 				),
				array(
					'name' => 'order_status',
 					'options' => self::getOrderFolders(),
 					'description' => 'You can decide on the order status new orders should have that have an uncertain authorization status.',
 					'type' => 'select',
 					'title' => 'New Order Status',
 					'value' => '0',
 					'required' => 1,
 				),
				array(
					'name' => 'form_position',
 					'options' => array('payment' => 'On payment selection page', 'checkout' => 'On checkout page', 'separate' => 'On separate page'),
 					'description' => 'Decide where the payment form should be displayed.',
 					'type' => 'select',
 					'title' => 'Payment Form Position',
 					'value' => 'checkout',
 					'required' => 1,
 				),
			);
		}
		if ($paymentId === 'unzercw_prepayment') {
			return array(
				array(
					'name' => 'status_authorized',
 					'options' => self::getOrderFolders(array(
					)),
 					'description' => 'This status is set, when the payment was successfulland it is authorized.',
 					'type' => 'select',
 					'title' => 'Authorized Status',
 					'value' => 'ORDERFOLDER_NEW',
 					'required' => 1,
 				),
				array(
					'name' => 'status_uncertain',
 					'options' => self::getOrderFolders(array(
					)),
 					'description' => 'You can specify the order status for new orders thathave an uncertain authorisation status.',
 					'type' => 'select',
 					'title' => 'Uncertain Status',
 					'value' => 'ORDERFOLDER_PROBLEMS',
 					'required' => 1,
 				),
				array(
					'name' => 'status_cancelled',
 					'options' => self::getOrderFolders(array(
						'no_status_change' => 'Don\'t change order status',
 					)),
 					'description' => 'You can specify the order status when an order iscancelled.',
 					'type' => 'select',
 					'title' => 'Cancelled Status',
 					'value' => 'ORDERFOLDER_FINISHED',
 					'required' => 1,
 				),
				array(
					'name' => 'status_captured',
 					'options' => self::getOrderFolders(array(
						'no_status_change' => 'Don\'t change order status',
 					)),
 					'description' => 'You can specify the order status for orders that arecaptured either directly after the order or manually in thebackend.',
 					'type' => 'select',
 					'title' => 'Captured Status',
 					'value' => 'no_status_change',
 					'required' => 1,
 				),
				array(
					'name' => 'send_basket',
 					'options' => array(
						'no' => 'Do not send',
 						'yes' => 'Send Basket',
 					),
 					'description' => 'Should the invoice items be transmitted toUnzer? This slightly increases theprocessing time due to an additional request, and may cause issuesfor certain quantity / price combinations.',
 					'type' => 'select',
 					'title' => 'Send Basket',
 					'value' => 'no',
 					'required' => 1,
 				),
				array(
					'name' => 'send_customer',
 					'options' => array(
						'no' => 'Do not send',
 						'yes' => 'Send Customer',
 					),
 					'description' => 'Should customer data be transmitted toUnzer? This slightly increases theprocessing time due to an additional request, but may allow e.g.saving the payment method to the customer.',
 					'type' => 'select',
 					'title' => 'Send Customer',
 					'value' => 'no',
 					'required' => 1,
 				),
				array(
					'name' => 'authorizationMethod',
 					'options' => array(
						'AjaxAuthorization' => 'Ajax Authorization',
 					),
 					'description' => 'Select the authorization method to use for processing this payment method.',
 					'type' => 'select',
 					'title' => 'Authorization Method',
 					'value' => 'AjaxAuthorization',
 					'required' => 1,
 				),
				array(
					'name' => 'min_order_total',
 					'description' => 'This payment method is only available in case the order total is equal to or greater than the specified amount.',
 					'type' => 'str',
 					'title' => 'Minimal Order Total',
 					'value' => '',
 					'required' => 0,
 				),
				array(
					'name' => 'max_order_total',
 					'description' => 'This payment method is only available in case the order total is equal to or less than the specified amount.',
 					'type' => 'str',
 					'title' => 'Maximal Order Total',
 					'value' => '',
 					'required' => 0,
 				),
				array(
					'name' => 'order_status',
 					'options' => self::getOrderFolders(),
 					'description' => 'You can decide on the order status new orders should have that have an uncertain authorization status.',
 					'type' => 'select',
 					'title' => 'New Order Status',
 					'value' => '0',
 					'required' => 1,
 				),
				array(
					'name' => 'form_position',
 					'options' => array('payment' => 'On payment selection page', 'checkout' => 'On checkout page', 'separate' => 'On separate page'),
 					'description' => 'Decide where the payment form should be displayed.',
 					'type' => 'select',
 					'title' => 'Payment Form Position',
 					'value' => 'checkout',
 					'required' => 1,
 				),
			);
		}
		if ($paymentId === 'unzercw_eps') {
			return array(
				array(
					'name' => 'status_authorized',
 					'options' => self::getOrderFolders(array(
					)),
 					'description' => 'This status is set, when the payment was successfulland it is authorized.',
 					'type' => 'select',
 					'title' => 'Authorized Status',
 					'value' => 'ORDERFOLDER_NEW',
 					'required' => 1,
 				),
				array(
					'name' => 'status_uncertain',
 					'options' => self::getOrderFolders(array(
					)),
 					'description' => 'You can specify the order status for new orders thathave an uncertain authorisation status.',
 					'type' => 'select',
 					'title' => 'Uncertain Status',
 					'value' => 'ORDERFOLDER_PROBLEMS',
 					'required' => 1,
 				),
				array(
					'name' => 'status_cancelled',
 					'options' => self::getOrderFolders(array(
						'no_status_change' => 'Don\'t change order status',
 					)),
 					'description' => 'You can specify the order status when an order iscancelled.',
 					'type' => 'select',
 					'title' => 'Cancelled Status',
 					'value' => 'ORDERFOLDER_FINISHED',
 					'required' => 1,
 				),
				array(
					'name' => 'status_captured',
 					'options' => self::getOrderFolders(array(
						'no_status_change' => 'Don\'t change order status',
 					)),
 					'description' => 'You can specify the order status for orders that arecaptured either directly after the order or manually in thebackend.',
 					'type' => 'select',
 					'title' => 'Captured Status',
 					'value' => 'no_status_change',
 					'required' => 1,
 				),
				array(
					'name' => 'send_basket',
 					'options' => array(
						'no' => 'Do not send',
 						'yes' => 'Send Basket',
 					),
 					'description' => 'Should the invoice items be transmitted toUnzer? This slightly increases theprocessing time due to an additional request, and may cause issuesfor certain quantity / price combinations.',
 					'type' => 'select',
 					'title' => 'Send Basket',
 					'value' => 'no',
 					'required' => 1,
 				),
				array(
					'name' => 'send_customer',
 					'options' => array(
						'no' => 'Do not send',
 						'yes' => 'Send Customer',
 					),
 					'description' => 'Should customer data be transmitted toUnzer? This slightly increases theprocessing time due to an additional request, but may allow e.g.saving the payment method to the customer.',
 					'type' => 'select',
 					'title' => 'Send Customer',
 					'value' => 'no',
 					'required' => 1,
 				),
				array(
					'name' => 'authorizationMethod',
 					'options' => array(
						'AjaxAuthorization' => 'Ajax Authorization',
 					),
 					'description' => 'Select the authorization method to use for processing this payment method.',
 					'type' => 'select',
 					'title' => 'Authorization Method',
 					'value' => 'AjaxAuthorization',
 					'required' => 1,
 				),
				array(
					'name' => 'min_order_total',
 					'description' => 'This payment method is only available in case the order total is equal to or greater than the specified amount.',
 					'type' => 'str',
 					'title' => 'Minimal Order Total',
 					'value' => '',
 					'required' => 0,
 				),
				array(
					'name' => 'max_order_total',
 					'description' => 'This payment method is only available in case the order total is equal to or less than the specified amount.',
 					'type' => 'str',
 					'title' => 'Maximal Order Total',
 					'value' => '',
 					'required' => 0,
 				),
				array(
					'name' => 'order_status',
 					'options' => self::getOrderFolders(),
 					'description' => 'You can decide on the order status new orders should have that have an uncertain authorization status.',
 					'type' => 'select',
 					'title' => 'New Order Status',
 					'value' => '0',
 					'required' => 1,
 				),
				array(
					'name' => 'form_position',
 					'options' => array('payment' => 'On payment selection page', 'checkout' => 'On checkout page', 'separate' => 'On separate page'),
 					'description' => 'Decide where the payment form should be displayed.',
 					'type' => 'select',
 					'title' => 'Payment Form Position',
 					'value' => 'checkout',
 					'required' => 1,
 				),
			);
		}
		if ($paymentId === 'unzercw_unzerbanktransfer') {
			return array(
				array(
					'name' => 'status_authorized',
 					'options' => self::getOrderFolders(array(
					)),
 					'description' => 'This status is set, when the payment was successfulland it is authorized.',
 					'type' => 'select',
 					'title' => 'Authorized Status',
 					'value' => 'ORDERFOLDER_NEW',
 					'required' => 1,
 				),
				array(
					'name' => 'status_uncertain',
 					'options' => self::getOrderFolders(array(
					)),
 					'description' => 'You can specify the order status for new orders thathave an uncertain authorisation status.',
 					'type' => 'select',
 					'title' => 'Uncertain Status',
 					'value' => 'ORDERFOLDER_PROBLEMS',
 					'required' => 1,
 				),
				array(
					'name' => 'status_cancelled',
 					'options' => self::getOrderFolders(array(
						'no_status_change' => 'Don\'t change order status',
 					)),
 					'description' => 'You can specify the order status when an order iscancelled.',
 					'type' => 'select',
 					'title' => 'Cancelled Status',
 					'value' => 'ORDERFOLDER_FINISHED',
 					'required' => 1,
 				),
				array(
					'name' => 'status_captured',
 					'options' => self::getOrderFolders(array(
						'no_status_change' => 'Don\'t change order status',
 					)),
 					'description' => 'You can specify the order status for orders that arecaptured either directly after the order or manually in thebackend.',
 					'type' => 'select',
 					'title' => 'Captured Status',
 					'value' => 'no_status_change',
 					'required' => 1,
 				),
				array(
					'name' => 'send_basket',
 					'options' => array(
						'no' => 'Do not send',
 						'yes' => 'Send Basket',
 					),
 					'description' => 'Should the invoice items be transmitted toUnzer? This slightly increases theprocessing time due to an additional request, and may cause issuesfor certain quantity / price combinations.',
 					'type' => 'select',
 					'title' => 'Send Basket',
 					'value' => 'no',
 					'required' => 1,
 				),
				array(
					'name' => 'send_customer',
 					'options' => array(
						'no' => 'Do not send',
 						'yes' => 'Send Customer',
 					),
 					'description' => 'Should customer data be transmitted toUnzer? This slightly increases theprocessing time due to an additional request, but may allow e.g.saving the payment method to the customer.',
 					'type' => 'select',
 					'title' => 'Send Customer',
 					'value' => 'no',
 					'required' => 1,
 				),
				array(
					'name' => 'authorizationMethod',
 					'options' => array(
						'AjaxAuthorization' => 'Ajax Authorization',
 					),
 					'description' => 'Select the authorization method to use for processing this payment method.',
 					'type' => 'select',
 					'title' => 'Authorization Method',
 					'value' => 'AjaxAuthorization',
 					'required' => 1,
 				),
				array(
					'name' => 'min_order_total',
 					'description' => 'This payment method is only available in case the order total is equal to or greater than the specified amount.',
 					'type' => 'str',
 					'title' => 'Minimal Order Total',
 					'value' => '',
 					'required' => 0,
 				),
				array(
					'name' => 'max_order_total',
 					'description' => 'This payment method is only available in case the order total is equal to or less than the specified amount.',
 					'type' => 'str',
 					'title' => 'Maximal Order Total',
 					'value' => '',
 					'required' => 0,
 				),
				array(
					'name' => 'order_status',
 					'options' => self::getOrderFolders(),
 					'description' => 'You can decide on the order status new orders should have that have an uncertain authorization status.',
 					'type' => 'select',
 					'title' => 'New Order Status',
 					'value' => '0',
 					'required' => 1,
 				),
				array(
					'name' => 'form_position',
 					'options' => array('payment' => 'On payment selection page', 'checkout' => 'On checkout page', 'separate' => 'On separate page'),
 					'description' => 'Decide where the payment form should be displayed.',
 					'type' => 'select',
 					'title' => 'Payment Form Position',
 					'value' => 'checkout',
 					'required' => 1,
 				),
			);
		}
		if ($paymentId === 'unzercw_unzerinstallment') {
			return array(
				array(
					'name' => 'effective_interest_rate',
 					'description' => 'The interest rate in percent that you enter here will be applied onto theinstalment. The rate must be above the amount that you have agreed up on with Unzer.',
 					'type' => 'str',
 					'title' => 'Applied Interest Rate',
 					'value' => '5.99',
 					'required' => 1,
 				),
				array(
					'name' => 'status_authorized',
 					'options' => self::getOrderFolders(array(
					)),
 					'description' => 'This status is set, when the payment was successfulland it is authorized.',
 					'type' => 'select',
 					'title' => 'Authorized Status',
 					'value' => 'ORDERFOLDER_NEW',
 					'required' => 1,
 				),
				array(
					'name' => 'status_uncertain',
 					'options' => self::getOrderFolders(array(
					)),
 					'description' => 'You can specify the order status for new orders thathave an uncertain authorisation status.',
 					'type' => 'select',
 					'title' => 'Uncertain Status',
 					'value' => 'ORDERFOLDER_PROBLEMS',
 					'required' => 1,
 				),
				array(
					'name' => 'status_cancelled',
 					'options' => self::getOrderFolders(array(
						'no_status_change' => 'Don\'t change order status',
 					)),
 					'description' => 'You can specify the order status when an order iscancelled.',
 					'type' => 'select',
 					'title' => 'Cancelled Status',
 					'value' => 'ORDERFOLDER_FINISHED',
 					'required' => 1,
 				),
				array(
					'name' => 'status_captured',
 					'options' => self::getOrderFolders(array(
						'no_status_change' => 'Don\'t change order status',
 					)),
 					'description' => 'You can specify the order status for orders that arecaptured either directly after the order or manually in thebackend.',
 					'type' => 'select',
 					'title' => 'Captured Status',
 					'value' => 'no_status_change',
 					'required' => 1,
 				),
				array(
					'name' => 'authorizationMethod',
 					'options' => array(
						'AjaxAuthorization' => 'Ajax Authorization',
 					),
 					'description' => 'Select the authorization method to use for processing this payment method.',
 					'type' => 'select',
 					'title' => 'Authorization Method',
 					'value' => 'AjaxAuthorization',
 					'required' => 1,
 				),
				array(
					'name' => 'min_order_total',
 					'description' => 'This payment method is only available in case the order total is equal to or greater than the specified amount.',
 					'type' => 'str',
 					'title' => 'Minimal Order Total',
 					'value' => '',
 					'required' => 0,
 				),
				array(
					'name' => 'max_order_total',
 					'description' => 'This payment method is only available in case the order total is equal to or less than the specified amount.',
 					'type' => 'str',
 					'title' => 'Maximal Order Total',
 					'value' => '',
 					'required' => 0,
 				),
				array(
					'name' => 'order_status',
 					'options' => self::getOrderFolders(),
 					'description' => 'You can decide on the order status new orders should have that have an uncertain authorization status.',
 					'type' => 'select',
 					'title' => 'New Order Status',
 					'value' => '0',
 					'required' => 1,
 				),
				array(
					'name' => 'form_position',
 					'options' => array('payment' => 'On payment selection page', 'checkout' => 'On checkout page', 'separate' => 'On separate page'),
 					'description' => 'Decide where the payment form should be displayed.',
 					'type' => 'select',
 					'title' => 'Payment Form Position',
 					'value' => 'checkout',
 					'required' => 1,
 				),
			);
		}
		if ($paymentId === 'unzercw_alipay') {
			return array(
				array(
					'name' => 'status_authorized',
 					'options' => self::getOrderFolders(array(
					)),
 					'description' => 'This status is set, when the payment was successfulland it is authorized.',
 					'type' => 'select',
 					'title' => 'Authorized Status',
 					'value' => 'ORDERFOLDER_NEW',
 					'required' => 1,
 				),
				array(
					'name' => 'status_uncertain',
 					'options' => self::getOrderFolders(array(
					)),
 					'description' => 'You can specify the order status for new orders thathave an uncertain authorisation status.',
 					'type' => 'select',
 					'title' => 'Uncertain Status',
 					'value' => 'ORDERFOLDER_PROBLEMS',
 					'required' => 1,
 				),
				array(
					'name' => 'status_cancelled',
 					'options' => self::getOrderFolders(array(
						'no_status_change' => 'Don\'t change order status',
 					)),
 					'description' => 'You can specify the order status when an order iscancelled.',
 					'type' => 'select',
 					'title' => 'Cancelled Status',
 					'value' => 'ORDERFOLDER_FINISHED',
 					'required' => 1,
 				),
				array(
					'name' => 'status_captured',
 					'options' => self::getOrderFolders(array(
						'no_status_change' => 'Don\'t change order status',
 					)),
 					'description' => 'You can specify the order status for orders that arecaptured either directly after the order or manually in thebackend.',
 					'type' => 'select',
 					'title' => 'Captured Status',
 					'value' => 'no_status_change',
 					'required' => 1,
 				),
				array(
					'name' => 'send_basket',
 					'options' => array(
						'no' => 'Do not send',
 						'yes' => 'Send Basket',
 					),
 					'description' => 'Should the invoice items be transmitted toUnzer? This slightly increases theprocessing time due to an additional request, and may cause issuesfor certain quantity / price combinations.',
 					'type' => 'select',
 					'title' => 'Send Basket',
 					'value' => 'no',
 					'required' => 1,
 				),
				array(
					'name' => 'send_customer',
 					'options' => array(
						'no' => 'Do not send',
 						'yes' => 'Send Customer',
 					),
 					'description' => 'Should customer data be transmitted toUnzer? This slightly increases theprocessing time due to an additional request, but may allow e.g.saving the payment method to the customer.',
 					'type' => 'select',
 					'title' => 'Send Customer',
 					'value' => 'no',
 					'required' => 1,
 				),
				array(
					'name' => 'authorizationMethod',
 					'options' => array(
						'AjaxAuthorization' => 'Ajax Authorization',
 					),
 					'description' => 'Select the authorization method to use for processing this payment method.',
 					'type' => 'select',
 					'title' => 'Authorization Method',
 					'value' => 'AjaxAuthorization',
 					'required' => 1,
 				),
				array(
					'name' => 'min_order_total',
 					'description' => 'This payment method is only available in case the order total is equal to or greater than the specified amount.',
 					'type' => 'str',
 					'title' => 'Minimal Order Total',
 					'value' => '',
 					'required' => 0,
 				),
				array(
					'name' => 'max_order_total',
 					'description' => 'This payment method is only available in case the order total is equal to or less than the specified amount.',
 					'type' => 'str',
 					'title' => 'Maximal Order Total',
 					'value' => '',
 					'required' => 0,
 				),
				array(
					'name' => 'order_status',
 					'options' => self::getOrderFolders(),
 					'description' => 'You can decide on the order status new orders should have that have an uncertain authorization status.',
 					'type' => 'select',
 					'title' => 'New Order Status',
 					'value' => '0',
 					'required' => 1,
 				),
				array(
					'name' => 'form_position',
 					'options' => array('payment' => 'On payment selection page', 'checkout' => 'On checkout page', 'separate' => 'On separate page'),
 					'description' => 'Decide where the payment form should be displayed.',
 					'type' => 'select',
 					'title' => 'Payment Form Position',
 					'value' => 'checkout',
 					'required' => 1,
 				),
			);
		}
		if ($paymentId === 'unzercw_wechatpay') {
			return array(
				array(
					'name' => 'status_authorized',
 					'options' => self::getOrderFolders(array(
					)),
 					'description' => 'This status is set, when the payment was successfulland it is authorized.',
 					'type' => 'select',
 					'title' => 'Authorized Status',
 					'value' => 'ORDERFOLDER_NEW',
 					'required' => 1,
 				),
				array(
					'name' => 'status_uncertain',
 					'options' => self::getOrderFolders(array(
					)),
 					'description' => 'You can specify the order status for new orders thathave an uncertain authorisation status.',
 					'type' => 'select',
 					'title' => 'Uncertain Status',
 					'value' => 'ORDERFOLDER_PROBLEMS',
 					'required' => 1,
 				),
				array(
					'name' => 'status_cancelled',
 					'options' => self::getOrderFolders(array(
						'no_status_change' => 'Don\'t change order status',
 					)),
 					'description' => 'You can specify the order status when an order iscancelled.',
 					'type' => 'select',
 					'title' => 'Cancelled Status',
 					'value' => 'ORDERFOLDER_FINISHED',
 					'required' => 1,
 				),
				array(
					'name' => 'status_captured',
 					'options' => self::getOrderFolders(array(
						'no_status_change' => 'Don\'t change order status',
 					)),
 					'description' => 'You can specify the order status for orders that arecaptured either directly after the order or manually in thebackend.',
 					'type' => 'select',
 					'title' => 'Captured Status',
 					'value' => 'no_status_change',
 					'required' => 1,
 				),
				array(
					'name' => 'send_basket',
 					'options' => array(
						'no' => 'Do not send',
 						'yes' => 'Send Basket',
 					),
 					'description' => 'Should the invoice items be transmitted toUnzer? This slightly increases theprocessing time due to an additional request, and may cause issuesfor certain quantity / price combinations.',
 					'type' => 'select',
 					'title' => 'Send Basket',
 					'value' => 'no',
 					'required' => 1,
 				),
				array(
					'name' => 'send_customer',
 					'options' => array(
						'no' => 'Do not send',
 						'yes' => 'Send Customer',
 					),
 					'description' => 'Should customer data be transmitted toUnzer? This slightly increases theprocessing time due to an additional request, but may allow e.g.saving the payment method to the customer.',
 					'type' => 'select',
 					'title' => 'Send Customer',
 					'value' => 'no',
 					'required' => 1,
 				),
				array(
					'name' => 'authorizationMethod',
 					'options' => array(
						'AjaxAuthorization' => 'Ajax Authorization',
 					),
 					'description' => 'Select the authorization method to use for processing this payment method.',
 					'type' => 'select',
 					'title' => 'Authorization Method',
 					'value' => 'AjaxAuthorization',
 					'required' => 1,
 				),
				array(
					'name' => 'min_order_total',
 					'description' => 'This payment method is only available in case the order total is equal to or greater than the specified amount.',
 					'type' => 'str',
 					'title' => 'Minimal Order Total',
 					'value' => '',
 					'required' => 0,
 				),
				array(
					'name' => 'max_order_total',
 					'description' => 'This payment method is only available in case the order total is equal to or less than the specified amount.',
 					'type' => 'str',
 					'title' => 'Maximal Order Total',
 					'value' => '',
 					'required' => 0,
 				),
				array(
					'name' => 'order_status',
 					'options' => self::getOrderFolders(),
 					'description' => 'You can decide on the order status new orders should have that have an uncertain authorization status.',
 					'type' => 'select',
 					'title' => 'New Order Status',
 					'value' => '0',
 					'required' => 1,
 				),
				array(
					'name' => 'form_position',
 					'options' => array('payment' => 'On payment selection page', 'checkout' => 'On checkout page', 'separate' => 'On separate page'),
 					'description' => 'Decide where the payment form should be displayed.',
 					'type' => 'select',
 					'title' => 'Payment Form Position',
 					'value' => 'checkout',
 					'required' => 1,
 				),
			);
		}
		if ($paymentId === 'unzercw_bcmc') {
			return array(
				array(
					'name' => 'status_authorized',
 					'options' => self::getOrderFolders(array(
					)),
 					'description' => 'This status is set, when the payment was successfulland it is authorized.',
 					'type' => 'select',
 					'title' => 'Authorized Status',
 					'value' => 'ORDERFOLDER_NEW',
 					'required' => 1,
 				),
				array(
					'name' => 'status_uncertain',
 					'options' => self::getOrderFolders(array(
					)),
 					'description' => 'You can specify the order status for new orders thathave an uncertain authorisation status.',
 					'type' => 'select',
 					'title' => 'Uncertain Status',
 					'value' => 'ORDERFOLDER_PROBLEMS',
 					'required' => 1,
 				),
				array(
					'name' => 'status_cancelled',
 					'options' => self::getOrderFolders(array(
						'no_status_change' => 'Don\'t change order status',
 					)),
 					'description' => 'You can specify the order status when an order iscancelled.',
 					'type' => 'select',
 					'title' => 'Cancelled Status',
 					'value' => 'ORDERFOLDER_FINISHED',
 					'required' => 1,
 				),
				array(
					'name' => 'status_captured',
 					'options' => self::getOrderFolders(array(
						'no_status_change' => 'Don\'t change order status',
 					)),
 					'description' => 'You can specify the order status for orders that arecaptured either directly after the order or manually in thebackend.',
 					'type' => 'select',
 					'title' => 'Captured Status',
 					'value' => 'no_status_change',
 					'required' => 1,
 				),
				array(
					'name' => 'placeholder_size',
 					'options' => array(
						'wide' => 'Wide (label from
							Unzer)
						',
 						'narrow' => 'Narrow (label from shop)',
 					),
 					'description' => 'How should elements fromUnzer be loaded? With narrow elementsthe element label is displayed by the store, with wide elements itisloaded via javascript by Unzer. Theinput elements are always loaded fromUnzer.',
 					'type' => 'select',
 					'title' => 'Element Size',
 					'value' => 'narrow',
 					'required' => 1,
 				),
				array(
					'name' => 'send_basket',
 					'options' => array(
						'no' => 'Do not send',
 						'yes' => 'Send Basket',
 					),
 					'description' => 'Should the invoice items be transmitted toUnzer? This slightly increases theprocessing time due to an additional request, and may cause issuesfor certain quantity / price combinations.',
 					'type' => 'select',
 					'title' => 'Send Basket',
 					'value' => 'no',
 					'required' => 1,
 				),
				array(
					'name' => 'send_customer',
 					'options' => array(
						'no' => 'Do not send',
 						'yes' => 'Send Customer',
 					),
 					'description' => 'Should customer data be transmitted toUnzer? This slightly increases theprocessing time due to an additional request, but may allow e.g.saving the payment method to the customer.',
 					'type' => 'select',
 					'title' => 'Send Customer',
 					'value' => 'no',
 					'required' => 1,
 				),
				array(
					'name' => 'authorizationMethod',
 					'options' => array(
						'AjaxAuthorization' => 'Ajax Authorization',
 					),
 					'description' => 'Select the authorization method to use for processing this payment method.',
 					'type' => 'select',
 					'title' => 'Authorization Method',
 					'value' => 'AjaxAuthorization',
 					'required' => 1,
 				),
				array(
					'name' => 'min_order_total',
 					'description' => 'This payment method is only available in case the order total is equal to or greater than the specified amount.',
 					'type' => 'str',
 					'title' => 'Minimal Order Total',
 					'value' => '',
 					'required' => 0,
 				),
				array(
					'name' => 'max_order_total',
 					'description' => 'This payment method is only available in case the order total is equal to or less than the specified amount.',
 					'type' => 'str',
 					'title' => 'Maximal Order Total',
 					'value' => '',
 					'required' => 0,
 				),
				array(
					'name' => 'order_status',
 					'options' => self::getOrderFolders(),
 					'description' => 'You can decide on the order status new orders should have that have an uncertain authorization status.',
 					'type' => 'select',
 					'title' => 'New Order Status',
 					'value' => '0',
 					'required' => 1,
 				),
				array(
					'name' => 'form_position',
 					'options' => array('payment' => 'On payment selection page', 'checkout' => 'On checkout page', 'separate' => 'On separate page'),
 					'description' => 'Decide where the payment form should be displayed.',
 					'type' => 'select',
 					'title' => 'Payment Form Position',
 					'value' => 'checkout',
 					'required' => 1,
 				),
			);
		}

		return array();
	}

	/**
	 * Return the configuration form fields of a payment method.
	 *
	 * @param integer $paymentId
	 * @return array
	 */
	public static function getConfigForm($paymentId)
	{
		$oStr = getStr();

		if (!isset(self::$_configFormCache[$paymentId])) {
			$result = self::getConfigElements($paymentId);

			foreach ($result as $key => $setting) {
				$result[$key]['key'] = substr($paymentId, strlen('unzercw_')) . '_' . $setting['name'];

				$value = oxRegistry::getConfig()->getShopConfVar($result[$key]['key'], null, 'module:unzercw');
				if ($value !== null) {
					$result[$key]['value'] = $value;
				} else {
					$result[$key]['value'] = self::unserializeValue($result[$key]['value'], $setting['type']);
				}

				if (is_array($result[$key]['value'])) {
					foreach ($result[$key]['value'] as $k => $v) {
						$result[$key]['value'][$k] = utf8_decode($v);
					}
				} else {
					$result[$key]['value'] = utf8_decode($result[$key]['value']);
				}

				switch ($setting['type']) {
					case 'multilang':
						if (is_string($result[$key]['value'])) {
							$result[$key]['value'] = $oStr->htmlentities($result[$key]['value']);
						} else {
							foreach ($result[$key]['value'] as $k => $v) {
								$result[$key]['value'][$k] = $oStr->htmlentities($v);
							}
						}
						break;
					case 'multiselect':
						break;
					case 'file':
						$result[$key]['options'] = UnzerCwHelper::getFileOptions($result[$key]['allowedFileExtensions']);
						break;
					default:
						$result[$key]['value'] = $oStr->htmlentities($result[$key]['value']);
						break;
				}
			}
			self::$_configFormCache[$paymentId] = $result;
		}

		return self::$_configFormCache[$paymentId];
	}

	/**
	 * Return a specific form fields of a payment method.
	 *
	 * @param integer $paymentId
	 * @param string $name
	 * @return array|boolean
	 */
	protected static function getElement($paymentId, $name)
	{
		$elements = self::getConfigElements($paymentId);
		foreach ($elements as $element) {
			if ($element['name'] == $name) {
				return $element;
			}
		}

		return false;
	}

	/**
	 * Return default value of a specific field.
	 *
	 * @param integer $paymentId
	 * @param string $name
	 * @return mixed
	 */
	public static function getDefaultValue($paymentId, $name)
	{
		$element = self::getElement($paymentId, $name);
		if ($element === false) {
			return;
		}
		return self::unserializeValue($element['value'], $element['type']);
	}

	/**
	 * Return the value of a specific field.
	 *
	 * @param integer $paymentId
	 * @param string $name
	 * @param integer $shopId
	 * @return mixed
	 */
	public static function getConfigValue($paymentId, $name, $languageCode = null)
	{
		$configValueDb = oxRegistry::getConfig()->getShopConfVar(substr($paymentId, strlen('unzercw_')) . '_' . $name, null, 'module:unzercw');
		$element = self::getElement($paymentId, $name);
		$configValue = self::unserializeValue($configValueDb, $element['type']);
		if ($element['type'] == 'multilang') {
			if ($languageCode !== null) {
				if (is_array($configValue) && isset($configValue[$languageCode])) {
					return $configValue[$languageCode];
				} else {
					return '';
				}
			}
		} elseif ($element['type'] == 'file') {
			$defaultValue = self::getDefaultValue($paymentId, $name);
			if (empty($configValue) || $configValue == $defaultValue) {
				return UnzerCwHelper::getAssetResolver()->resolveAssetStream($defaultValue);
			} else {
				return new Customweb_Core_Stream_Input_File(UnzerCwHelper::getUploadDirectory() . $configValue);
			}
		} 
		if ($configValueDb === null) {
			$configValue = self::getDefaultValue($paymentId, $name);
		}

		return $configValue;
	}

	protected static function getOrderFolders($options = array())
	{
		$folders = oxRegistry::getConfig()->getConfigParam('aOrderfolder');
		foreach ($folders as $folder => $color) {
			$options[$folder] = $folder;
		}
		return $options;
	}

	protected static function unserializeValue($value, $type)
	{
		if ($type == 'multilang') {
			if (!is_array($value)) {
				$newValue = array();
				foreach (oxRegistry::getConfig()->getConfigParam('aLanguages') as $langKey => $lang) {
					$newValue[$langKey] = $value;
				}
				$value = $newValue;
			}
		}
		if ($type == 'multiselect') {
			if (empty($value)) {
				$value = array();
			}elseif(is_array($value)){
				$value = $value;	
			}else {			
				$value = explode(',', $value);
			}
		}

		return $value;
	}
}