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

require_once 'Customweb/Core/Logger/Factory.php';


class unzercw_oxorder extends unzercw_oxorder_parent
{
	/**
	 * @var Customweb_Core_ILogger
	 */
	private $loggerUNZ;

	protected $_tmpSessionData = null;

	protected $_forceEmail = false;

	public function __construct()
	{
		parent::__construct();

		$this->loggerUNZ = Customweb_Core_Logger_Factory::getLogger(get_class($this));
	}

	public function simulate(oxBasket $oBasket, $oUser)
	{
		// copies user info
		$this->_setUser($oUser);

		// payment information
		$oUserPayment = $this->_setPayment($oBasket->getPaymentId());

		oxNew('oxbasketitem')->disableCheckProduct(true);

		$blPsBasketReservationEnabled = $oBasket->getConfig()->getConfigParam('blPsBasketReservationEnabled');
		$oBasket->getConfig()->setConfigParam('blPsBasketReservationEnabled', false);

		// copies basket info
		$oBasket->onUpdate();
		$oBasket->calculateBasket();
		$this->_loadFromBasket($oBasket);

		$oBasket->getConfig()->setConfigParam('blPsBasketReservationEnabled', $blPsBasketReservationEnabled);

		oxNew('oxbasketitem')->disableCheckProduct(false);

		return $this;
	}

	public function finalizeOrder( \OxidEsales\Eshop\Application\Model\Basket $oBasket, $oUser, $blRecalculatingOrder = false )
	{
		$result = parent::finalizeOrder( $oBasket, $oUser, $blRecalculatingOrder );

		if ($result == oxOrder::ORDER_STATE_OK && !$blRecalculatingOrder && UnzerCwHelper::isUnzercwPaymentMethod($oBasket->getPaymentId())
			&& UnzerCwHelper::isCreateOrderBefore()) {
			$payment = new UnzerCwPaymentMethod($oBasket->getPaymentId());

			$adapter = UnzerCwHelper::getCheckoutAdapterByContext($payment->getOrderContext($this));
			$adapter->prepare($this, $payment);

			$this->loggerUNZ->logDebug('order ' . $this->getId() . ': status to PAYMENT_PENDING');
			$this->_setOrderStatus( 'PAYMENT_PENDING' );

			$this->oxorder__oxtransid = new oxField($adapter->getTransaction()->getTransactionId());
			$this->save();

			$adapter->getTransaction()->setSessionData($this->_tmpSessionData);
			$adapter->getTransaction()->setOrderNumber(oxDb::getDb()->getOne("SELECT oxordernr FROM oxorder where oxid = ?", array($adapter->getTransaction()->getOrderId())));
			$adapter->getTransaction()->setPaymentType(oxDb::getDb()->getOne("SELECT oxpaymenttype FROM oxorder where oxid = ?", array($adapter->getTransaction()->getOrderId())));
			UnzerCwHelper::getEntityManager()->persist($adapter->getTransaction());
		}

		return $result;
	}

	protected function _sendOrderByEmail( $oUser = null, $oBasket = null, $oPayment = null )
	{
		if (UnzerCwHelper::isUnzercwPaymentMethod($oBasket->getPaymentId())
			&& !$this->_forceEmail) {
			$this->_tmpSessionData = base64_encode(serialize(array(
				'oUser' => $oUser,
				'oBasket' => $oBasket,
				'oPayment' => $oPayment
			)));

			return self::ORDER_STATE_OK;
		}

		if (UnzerCwHelper::isUnzercwPaymentMethod($oBasket->getPaymentId())) {
			$isAdmin = $this->isAdmin();
			$this->setAdminMode(false);
			$result = parent::_sendOrderByEmail( $oUser, $oBasket, $oPayment );
			$this->setAdminMode($isAdmin);
			return $result;
		}

		return parent::_sendOrderByEmail( $oUser, $oBasket, $oPayment );
	}

	protected function _sendOrderByEmailForced( $oUser = null, $oBasket = null, $oPayment = null )
	{
		oxNew('oxbasketitem')->disableCheckProduct(true);

		$this->_forceEmail = true;
		$result = parent::_sendOrderByEmail( $oUser, $oBasket, $oPayment );
		$this->_forceEmail = false;

		oxNew('oxbasketitem')->disableCheckProduct(false);

		return $result;
	}

	public function finishOrder( $sessionData )
	{
		$this->_setOrderStatus( 'OK' );
		$this->save();

		// Backup variables
		$oBasket = $this->_oBasket;
		$oUser = $this->_oUser;
		$oPayment = $this->_oPayment;

		$this->_markVouchers($sessionData['oBasket'], $sessionData['oUser']);
		$this->_sendOrderByEmailForced( $sessionData['oUser'], $sessionData['oBasket'], $sessionData['oPayment'] );

		// Restore variables
		$this->_oUser    = $oUser;
		$this->_oBasket  = $oBasket;
		$this->_oPayment = $oPayment;
	}

	public function setPaymentPendingStatus()
	{
		$this->loggerUNZ->logDebug('order ' . $this->getId() . ': status to PAYMENT_PENDING');

		$this->_setOrderStatus( 'PAYMENT_PENDING' );
		$this->save();
	}

	public function setPaymentFailedStatus()
	{
		if ($this->oxorder__oxtransstatus->value == 'PAYMENT_PENDING') {
			$this->loggerUNZ->logDebug('order ' . $this->getId() . ': status to PAYMENT_FAILED');

			$this->_setOrderStatus( 'PAYMENT_FAILED' );
			$this->save();
		}
	}
	
	public function getTmpSessionData()
	{
		return $this->_tmpSessionData;
	}
}