<?php
/**
 * @category   DeutschePost Postident
 * @package    DeutschePost_Postident
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * DeutschePost_Postident_Controllers_CartController
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DeutschePost_Postident_CartController extends Mage_Core_Controller_Front_Action
{

    /**
     * handles the redirect to the shop after a E-POSTIDENT Request
     * 
     * @return void
     */
    public function backAction()
    {
        try {
            $postidentClient  =  Mage::getModel('postident/client');
            
            //Code for Parsing the redirect response
            $responseParams = $this->getRequest()->getParams();
            $clientHelper = Mage::helper('postident/client');
            $clientHelper->validateRedirectResponse($responseParams);

            //Request access ticket
            $accessToken =  $postidentClient->sendAccessTicketRequest($responseParams['code']);
            
            //Request identification data 
            $identData = $postidentClient->sendIdentDataRequest($accessToken);
            
            //Save identification data to quote
            Mage::helper('postident/client')->saveIdentDataToQuote($identData);
            
            //if customer is logged in save idenData from quote on customer

            if (true === Mage::helper('customer')->isLoggedIn()) {
                Mage::helper('postident/data')->saveIdentDataToCustomer();
            }
            
            //Check if the user passed the verification requirements
            if (true === Mage::getModel("postident/verification")->userIsVerified()) {
                //Success message, that the identification was passed successfully
                Mage::getSingleton('core/session')->addSuccess(
                     Mage::helper('postident')->__('Your data was verified successfully'));
            } else {
                //Identification passed successfully but the verification (by different criteria like age) failed
                throw new DeutschePost_Postident_Model_Verification_Exception(
                    "Identification passed successfully but verification failed"
                );
            }
            
            //Redirect in Checkout mit success
            $this->_redirect('checkout/onepage/');
            
        } catch (DeutschePost_Postident_Helper_Client_Exception $e) {
            $this->handleVerificationError($e, Mage::helper('postident')->__('The identification failed. Please try again.'));
        } catch (DeutschePost_Postident_Model_Client_Exception $e) {
            $this->handleVerificationError($e, Mage::helper('postident')->__('The identification failed. Please try again.'));
        } catch (DeutschePost_Postident_Model_Verification_Exception $e) {
            $this->handleVerificationError(
                $e,
                Mage::helper('postident')->__('The identification was passed succesfully but the verification to enter the checkout failed.')
            );
        } catch (Exception $e) {
            $this->handleVerificationError($e, Mage::helper('postident')->__('A system-error occured. Please try again.'));
        }
    }
    
    /**
     * 
     * handle the occurence of a verification error
     * and log the error and redirects the customer to cart
     * 
     * @param mixed $exception
     * @param string $frontendMessage
     * 
     * @return void
     */
    protected function handleVerificationError($exception, $frontendMessage)
    {
        Mage::helper('postident')->log(sprintf('Catched %s with message: %s', 
            get_class($exception),
            $exception->getMessage()
        ));
        Mage::getSingleton('core/session')->addError($frontendMessage);
        $this->_redirect('checkout/cart');
    }
}



