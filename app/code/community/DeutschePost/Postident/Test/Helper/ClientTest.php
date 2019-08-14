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
 * DeutschePost_Postident_Test_Helper_Client
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DeutschePost_Postident_Test_Helper_ClientTest extends EcomDev_PHPUnit_Test_Case
{

    
    /**
     * @var Mage_Core_Model_Store
     */
    protected $store;

    /**
     * @var DeutschePost_Postident_Model_Config
     */
    protected $config;

    public function setUp()
    {
        $this->store = Mage::app()->getStore(0)->load(0);
        $this->config = Mage::getModel('postident/config');
        parent::setup();
    }

    public function testValidateRedirectResponseNull()

    {
        $response = array();
        $helper = Mage::helper('postident/client');
        $this->setExpectedException('DeutschePost_Postident_Helper_Client_Exception');
        $helper->validateRedirectResponse($response);
        
    }
    
    public function testValidateRedirectResponseParameterStateMissing()
    {
        $response = array(
            'code' => 'test'
        );
        $helper = Mage::helper('postident/client');
        $this->setExpectedException('DeutschePost_Postident_Helper_Client_Exception');
        $helper->validateRedirectResponse($response);
        
    }
    
     public function testValidateRedirectResponseParameterCodeMissing()
    {
        $response = array(
            'state' => 'testState'
        );
        $helper = Mage::helper('postident/client');
        $this->setExpectedException('DeutschePost_Postident_Helper_Client_Exception');
        $helper->validateRedirectResponse($response);
        
    }
    
    
    public function testValidateRedirectResponseQuoteIdFailure()
    {
        $response = array(
            'code'  => 'test',
            'state' => '12'
        );
        $helperMock = $this->getHelperMock('postident/data', array(
            'getQuote'
        ));
        $quote = Mage::getModel('sales/quote');
        $quote->setId('5');
        $helperMock->expects($this->any())
            ->method('getQuote')
            ->will($this->returnValue($quote));
        $helper = Mage::helper('postident/client');
        $this->setExpectedException('DeutschePost_Postident_Helper_Client_Exception');
        $helper->validateRedirectResponse($response);
        
        
    }
    
    /**
     *  @test
     *  @loadExpectation redirectLink.yaml
     */
    public function testBuildRedirectLink()
    {
        
        $quote = Mage::getModel('sales/quote');
        $quote->setId(9);

        $helperMock = $this->getHelperMock('postident/data', array(
            'getQuote',
        ));
        $helperMock->expects($this->once())
            ->method('getQuote')
            ->will($this->returnValue($quote));
        $this->replaceByMock('helper', 'postident/data', $helperMock);
        
        $configModelMock = $this->getModelMock('postident/config', array(
            'getPostidentUrl',
            'getClientId',
            'getSelectedIdCard'
        ));
        $configModelMock->expects($this->once())
            ->method('getPostidentUrl')
            ->will($this->returnValue('https://ident.epost-gka.de/oauth2/login'));
        $configModelMock->expects($this->once())
            ->method('getClientId')
            ->will($this->returnValue('05337686-ff03-4c9c-aff9-6a3823e0faf0'));
        $configModelMock->expects($this->once())
            ->method('getSelectedIdCard')
            ->will($this->returnValue('10'));
        $this->replaceByMock('model', 'postident/config', $configModelMock);

        $this->store->resetConfig();
        $this->store->setConfig('web/secure/base_url', 'https://example.com/'); 
        $this->store->setConfig('web/unsecure/base_url', 'https://example.com/'); 
        Mage::app()->getStore()->setName('English');
        $redirectLink = $this->_getExpectations()->getLink();
        $this->assertEquals($redirectLink, Mage::helper('postident/client')->buildRedirectLink());
        $this->store->resetConfig();
        
    }
    
    /**
     *  @test
     *  @loadExpectation postidentQuoteVerificationData.yaml
     */
    public function testSaveIdentDataToQuote()
    {
        $simpleXML = simplexml_load_string($this->_getExpectations()->getXml());
        
        //Mock Date
        $dateMock = $this->getModelMock('core/date', array(
            'date'
        ));
        $dateMock->expects($this->any())
            ->method('date')
            ->will($this->returnValue($this->_getExpectations()->getDate()));
        $this->replaceByMock('model', 'core/date', $dateMock);
        
        //Mock verification
        $verificationMock = $this->getModelMock('postident/verification', array(
            'setPostidentVerificationDataToQuote'
        ));
        
        $verificationMock->expects($this->any())
            ->method('setPostidentVerificationDataToQuote')
            ->with($this->equalTo(unserialize($this->_getExpectations()->getIdentdata())));
        $this->replaceByMock('model', 'postident/verification', $verificationMock);
        
        Mage::helper('postident/client')->saveIdentDataToQuote($simpleXML);
    }
}