<?php

class EMerchantPay_Genesis_Block_Form_Checkout extends Mage_Payment_Block_Form
{
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('emerchantpay/form/checkout.phtml');
	}
}