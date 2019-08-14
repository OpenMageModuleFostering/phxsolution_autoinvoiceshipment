<?php
/*
/**
 * PHXSolution Autoinvoice and shipment
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so you can be sent a copy immediately.
 *
 * Original code copyright (c) 2008 Irubin Consulting Inc. DBA Varien
 *
 * @category   Autoinvoiceshipment Model
 * @package    Phxsolution_Autoinvoiceshipment
 * @author     Prakash Vaniya
 * @contact    contact@phxsolution.com
 * @site       www.phxsolution.com
 * @copyright  Copyright (c) 2014 PHXSolution
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
?>
<?php
class Phxsolution_Autoinvoiceshipment_Model_Observer
{
	public function automaticallyInvoiceShipCompleteOrder(Varien_Event_Observer $observer)
	{
		if(Mage::helper('autoinvoiceshipment')->getIsEnabled())
        {
            //Mage::dispatchEvent('admin_session_user_login_success', array('user'=>$user));
    		//$user = $observer->getEvent()->getUser();
    		//$user->doSomething();
    		
    		$order = $observer->getEvent()->getOrder();
    		
    		$_totalData = Mage::getModel('sales/order')->load($order->getId());
    		$shipping_address = $_totalData->getShippingAddress();
    		//$order->getWeight()/2.2046."<br/>"; 
    		//date('Y-m-d',strtotime($order->getCreatedAt()));
    		//echo "<pre>"; print_r($shipping_address->getData());
    		//echo "<pre>"; print_r($_totalData->getData()); die;
    		
            $orders = Mage::getModel('sales/order_invoice')->getCollection()
                            ->addAttributeToFilter('order_id', array('eq'=>$order->getId()));
            $orders->getSelect()->limit(1);
            if ((int)$orders->count() !== 0) {
                return $this;
            }
            if ($order->getState() == Mage_Sales_Model_Order::STATE_NEW) {
                try {
                    if(!$order->canInvoice()) {
                        $order->addStatusHistoryComment('Phxsolution_Autoinvoiceshipment: Order cannot be invoiced.', false);
                        $order->save();
                    }
                    //START Handle Invoice
                    $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
                    $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
                    $invoice->register();
                    $invoice->getOrder()->setCustomerNoteNotify(false);
                    $invoice->getOrder()->setIsInProcess(true);
                    $order->addStatusHistoryComment('Automatically INVOICED by Phxsolution_Autoinvoiceshipment.', false);
                    $transactionSave = Mage::getModel('core/resource_transaction')
                        ->addObject($invoice)
                        ->addObject($invoice->getOrder());
                    $transactionSave->save();
                    //END Handle Invoice
    				
                    /*//START Handle Shipment
    				$shipment = $order->prepareShipment();
                    $shipment->register();
                    $order->setIsInProcess(true);
                    $order->addStatusHistoryComment('Automatically SHIPPED by Phxsolution_Autoinvoiceshipment.', false);

    				if(isset($AirwayBillNumber) && $AirwayBillNumber != '')
    				{
    					$track = Mage::getModel('sales/order_shipment_track')
    								->setNumber($AirwayBillNumber) //tracking number / awb number
    								->setCarrierCode('DHL') //carrier code
    								->setTitle('DHL'); //carrier title
    					$shipment->addTrack($track);
    				}
                    $transactionSave = Mage::getModel('core/resource_transaction')
                        ->addObject($shipment)
                        ->addObject($shipment->getOrder())
                        ->save();
                    //END Handle Shipment*/	
    				
                } catch (Exception $e) {
                    $order->addStatusHistoryComment('Phxsolution_Autoinvoiceshipment: Exception occurred during automaticallyInvoiceShipCompleteOrder action. Exception message: '.$e->getMessage(), false);
                    $order->save();
                }
            }
        }
        return $this;
	}		
}