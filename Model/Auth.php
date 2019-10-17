<?php

namespace Magento\Braintree\Model;

use Magento\Braintree\Api\AuthInterface;
use Magento\Braintree\Api\Data\AuthDataInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class Auth implements AuthInterface
{
    /**
     * @var \Magento\Braintree\Api\Data\AuthDataInterfaceFactory
     */
    private $authData;

    /**
     * @var ApplePay\Ui\ConfigProvider
     */
    private $applePayConfigProvider;

    /**
     * @var Ui\ConfigProvider
     */
    private $configProvider;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $url;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Auth constructor.
     * @param \Magento\Braintree\Api\Data\AuthDataInterfaceFactory $authData
     * @param ApplePay\Ui\ConfigProvider $applePayConfigProvider
     * @param Ui\ConfigProvider $configProvider
     * @param UrlInterface $url
     * @param CustomerSession $customerSession
     * @param StoreManagerInterface $storeManagerInterface
     */
    public function __construct(
        \Magento\Braintree\Api\Data\AuthDataInterfaceFactory $authData,
        ApplePay\Ui\ConfigProvider $applePayConfigProvider,
        Ui\ConfigProvider $configProvider,
        UrlInterface $url,
        CustomerSession $customerSession,
        StoreManagerInterface $storeManagerInterface
    ) {
        $this->authData = $authData;
        $this->applePayConfigProvider = $applePayConfigProvider;
        $this->configProvider = $configProvider;
        $this->url = $url;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManagerInterface;
    }

    /**
     * @inheritdoc
     */
    public function get(): AuthDataInterface
    {
        /** @var $data \Magento\Braintree\Api\Data\AuthDataInterface */
        $data = $this->authData->create();
        $data->setClientToken($this->getClientToken());
        $data->setApplePayDisplayName($this->getApplePayDisplayName());
        $data->setActionSuccess($this->getActionSuccess());
        $data->setIsLoggedIn($this->getIsLoggedIn());
        $data->setStoreCode($this->getStoreCode());

        return $data;
    }

    protected function getClientToken()
    {
        return $this->configProvider->getClientToken();
    }

    protected function getApplePayDisplayName()
    {
        return $this->applePayConfigProvider->getMerchantName();
    }

    protected function getActionSuccess()
    {
        return $this->url->getUrl('checkout/onepage/success', ['_secure' => true]);
    }

    protected function getIsLoggedIn()
    {
        return (bool) $this->customerSession->isLoggedIn();
    }

    protected function getStoreCode()
    {
        return $this->storeManager->getStore()->getCode();
    }
}
