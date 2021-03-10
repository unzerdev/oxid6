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



class unzercw_transaction_list extends oxAdminList
{
    /**
     * Name of chosen object class (default null).
     *
     * @var string
     */
    protected $_sListClass = 'unzercw_transaction';

    /**
     * Enable/disable sorting by DESC (SQL) (defaultfalse - disable).
     *
     * @var bool
     */
    protected $_blDesc = true;

    /**
     * Default SQL sorting parameter (default null).
     *
     * @var string
     */
    protected $_sDefSortField = "createdOn";

    /**
     * @return string
     */
    public function render()
    {
        parent::render();

        return "unzercw_transaction_list.tpl";
    }

    /**
     * Builds and returns SQL query string. Adds additional order check.
     *
     * @param object $oListObject list main object
     *
     * @return string
     */
    protected function _buildSelectString( $oListObject = null )
    {
        $oListItem = $this->getItemListBaseObject();
        $iLangId = $oListItem->isMultilang() ? $oListItem->getLanguage() : oxRegistry::getLang()->getBaseLanguage();
        $paymentView = getViewName('oxpayments', $iLangId);

        // We should not use here a join since mysql cannot use the index on the 'created' when we have a join in it. Seems as mysql prefers
        // scanning the whole table in case of a join.
        $sSql = 'select ' . $this->_sListClass . '.*, ' . $paymentView . '.oxdesc as paymentmethodname from ' .
           $this->_sListClass . ', ' . $paymentView .
           ' where ' . $this->_sListClass . '.paymentType = ' . $paymentView . '.oxid AND ' .
           $this->_sListClass . '.shopId = \'' . oxRegistry::getConfig()->getShopId() . '\' ';

        return $sSql;
    }
}
