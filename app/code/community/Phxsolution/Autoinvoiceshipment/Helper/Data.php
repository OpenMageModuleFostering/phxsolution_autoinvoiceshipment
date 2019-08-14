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
 * @category   Autoinvoiceshipment Helper
 * @package    Phxsolution_Autoinvoiceshipment
 * @author     Prakash Vaniya
 * @contact    contact@phxsolution.com
 * @site       www.phxsolution.com
 * @copyright  Copyright (c) 2014 PHXSolution
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
?>
<?php
class Phxsolution_Autoinvoiceshipment_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getIsEnabled()
    {
      return Mage::getStoreConfig('autoinvoiceshipment_section/settings/enabled');
    }
}