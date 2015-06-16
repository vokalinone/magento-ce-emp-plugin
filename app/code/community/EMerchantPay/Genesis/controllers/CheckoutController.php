<?php
/*
 * Copyright (C) 2015 eMerchantPay Ltd.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @author      eMerchantPay
 * @copyright   2015 eMerchantPay Ltd.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2 (GPL-2.0)
 */

/**
 * Class EMerchantPay_Genesis_CheckoutController
 *
 * Front-end controller for Checkout method
 */
class EMerchantPay_Genesis_CheckoutController extends Mage_Core_Controller_Front_Action
{
    /**
     * Process an incoming Genesis Notification
     * If it appears valid, do a reconcile and
     * use the reconcile data to save details
     * about the transaction
     *
     * @see Genesis_API_Documentation \ notification_url
     *
     * @return void
     */
    public function notifyAction()
    {
        // Notifications are only POST, deny everything else
        if (!$this->getRequest()->isPost()) {
            return;
        }

        try {
            /** @var EMerchantPay_Genesis_Helper_Data $helper */
            $helper = Mage::helper('emerchantpay');
            /** @var EMerchantPay_Genesis_Model_Checkout $checkout */
            $checkout = Mage::getModel('emerchantpay/checkout');

            $helper->initClient($checkout->getCode());

            $notification = new \Genesis\API\Notification(
                $this->getRequest()->getPost()
            );

            if ($notification->isAuthentic()) {
                $notification->initReconciliation();

                $reconcile = $notification->getReconciliationObject();

                if (isset($reconcile->payment_transaction)) {
                    switch ($reconcile->payment_transaction->transaction_type) {
                        case \Genesis\API\Constants\Transaction\Types::AUTHORIZE:
                        case \Genesis\API\Constants\Transaction\Types::AUTHORIZE_3D:
                            $checkout->processAuthorizeNotification($reconcile);
                            break;
                        case \Genesis\API\Constants\Transaction\Types::SALE:
                        case \Genesis\API\Constants\Transaction\Types::SALE_3D:
                            $checkout->processCaptureNotification($reconcile);
                            break;
                        default:
                            break;
                    }

                    $this->getResponse()->setHeader('Content-type', 'application/xml');

                    $this->getResponse()->setBody(
                        $notification->generateResponse()
                    );
                }
            }
        } catch (Exception $exception) {
            Mage::logException($exception);
        }
    }

    /**
     * When a customer chooses eMerchantPay Checkout on
     * Checkout/Payment page, show them a "transition"
     * page where you notify them, that they will be
     * redirected to a new website.
     *
     * @see Genesis_API_Documentation \ notification_url
     *
     * @return void
     */
    public function redirectAction()
    {
        /** @var EMerchantPay_Genesis_Helper_Data $helper */
        $helper = Mage::helper('emerchantpay');

        $helper->redirectIfNotLoggedIn();

        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('emerchantpay/redirect_checkout')->toHtml()
        );
    }

    /**
     * Customer landing page for successful payment
     *
     * @see Genesis_API_Documentation \ return_success_url
     *
     * @return void
     */
    public function successAction()
    {
        /** @var EMerchantPay_Genesis_Helper_Data $helper */
        $helper = Mage::helper('emerchantpay');

        $helper->redirectIfNotLoggedIn();

        $this->_redirect('checkout/onepage/success', array('_secure' => true));
    }

    /**
     * Customer landing page for unsuccessful payment
     *
     * @see Genesis_API_Documentation \ return_failure_url
     *
     * @return void
     */
    public function failureAction()
    {
        /** @var EMerchantPay_Genesis_Helper_Data $helper */
        $helper = Mage::helper('emerchantpay');

        $helper->redirectIfNotLoggedIn();

        $helper->restoreQuote();

        $helper->getCheckoutSession()->addError(
            $helper->__('We were unable to process your payment! Please check your input or try again later.')
        );

        $this->_redirect('checkout/cart', array('_secure' => true));
    }

    /**
     * Customer landing page for cancelled payment
     *
     * @return void
     */
    public function cancelAction()
    {
        /** @var EMerchantPay_Genesis_Helper_Data $helper */
        $helper = Mage::helper('emerchantpay');

        $helper->redirectIfNotLoggedIn();

        $helper->restoreQuote($shouldCancel = true);

        $helper->getCheckoutSession()->addSuccess(
            $helper->__('Your payment session has been cancelled successfully!')
        );

        $this->_redirect('checkout/cart');
    }

    public function debugAction()
    {

    }
}