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

require_once 'Customweb/Payment/Entity/AbstractTransaction.php';


/**
 * @Entity(tableName = 'unzercw_transaction')
 *
 * @Filter(name = 'loadByOrderId', where = 'orderId = >orderId', orderBy = 'createdOn ASC')
 */
class UnzerCwTransaction extends Customweb_Payment_Entity_AbstractTransaction
{
	private $oxid;

	private $sessionData;

	private $sessionDataDeprecated;

	private $orderCache;

	private $shopId;

	private $orderNumber;

	private $paymentType;

	/**
	 * @Column(type = 'varchar', size = '32')
	 */
	public function getOxid(){
		return $this->oxid;
	}

	public function setOxid($oxid){
		$this->oxid = $oxid;
		return $this;
	}

	/**
	 * @Column(type = 'binaryObject', name='sessionDataBinary')
	 */
	public function getSessionData()
	{
		return $this->sessionData;
	}

	public function setSessionData($sessionData)
	{
		$this->sessionData = $sessionData;
		return $this;
	}

	/**
	 * @Column(type = 'integer', size = '11')
	 */
	public function getOrderNumber(){
		return $this->orderNumber;
	}

	public function setOrderNumber($orderNumber){
		$this->orderNumber = $orderNumber;
		return $this;
	}

	/**
	 * @Column(type = 'varchar', size = '32')
	 */
	public function getPaymentType(){
		return $this->paymentType;
	}

	public function setPaymentType($paymentType){
		$this->paymentType = $paymentType;
		return $this;
	}

	/**
	 * @Column(type = 'text', name='sessionData')
	 *
	 * @return array
	 */
	public function getSessionDataDeprecated(){

		return $this->sessionDataDeprecated;
	}

	public function setSessionDataDeprecated($data){
		if(!empty($data)){
			$this->sessionData = $data;
		}
		$this->sessionDataDeprecated = $data;
		return $this;
	}



	/**
	 * @Column(type = 'varchar', size = '32')
	 */
	public function getShopId()
	{
		return $this->shopId;
	}

	public function setShopId($shopId)
	{
		$this->shopId = $shopId;
		return $this;
	}

	public function getOrder($cached = true)
	{
		$orderId = $this->getOrderId();
		if ((!$cached || $this->orderCache == null) && !empty($orderId)) {
			$order = oxNew('oxorder');
			$order->load($this->getOrderId());
			$this->orderCache = $order;
		}
		return $this->orderCache;
	}

	public function onBeforeSave(Customweb_Database_Entity_IManager $entityManager) {
		if($this->isSkipOnSafeMethods()){
			return;
		}
		if ($this->getOxid() === null) {
			$this->setOxid(oxUtilsObject::getInstance()->generateUID());
		}
		parent::onBeforeSave($entityManager);
		$this->sessionDataDeprecated = '';
	}

	protected function updateOrderStatus(Customweb_Database_Entity_IManager $entityManager, $orderStatus, $orderStatusSettingKey) {
		$order = $this->getOrder();
		$order->oxorder__oxfolder = new oxField($orderStatus);
		$order->save();
	}

	protected function authorize(Customweb_Database_Entity_IManager $entityManager) {
		$this->getTransactionObject()->getPaymentMethod()->authorizeTransaction($this);
	}
}