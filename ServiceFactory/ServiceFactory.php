<?php

namespace Wandi\OAuthBundle\ServiceFactory;

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Exception\Exception;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Http\Client\CurlClient;
use OAuth\OAuth1\Token\StdOAuth1Token;
use OAuth\OAuth2\Token\StdOAuth2Token;
use OAuth\ServiceFactory as BaseServiceFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ServiceFactory
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var BaseServiceFactory
     */
    private $factory;

    /**
     * @var TokenStorageInterface
     */
    private $storage;

    /**
     * @var array
     */
    private $serviceCache = array();

    /**
     * @param ContainerInterface $container
     * @param BaseServiceFactory $factory
     * @param TokenStorageInterface $storage
     */
    public function __construct(ContainerInterface $container, BaseServiceFactory $factory, TokenStorageInterface $storage)
    {
        $this->container = $container;
        $this->factory = $factory;
        $this->storage = $storage;
    }

    /**
     * @param $resourceOwnerName
     * @return \OAuth\Common\Service\ServiceInterface
     * @throws Exception
     */
    public function createService($resourceOwnerName)
    {
        if (!isset(ResourceOwners::$all[$resourceOwnerName])) {
            throw new Exception('Resource owner '.$resourceOwnerName.' is not available');
        }

        if (isset($this->serviceCache[$resourceOwnerName])) {
            return $this->serviceCache[$resourceOwnerName];
        }

        $lowerResourceOwnerName = $string = strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', "_$1", $resourceOwnerName));

        $callbackRoute = $this->container->getParameter('wandi_oauth.resource_owners.'.$lowerResourceOwnerName.'.callback_route');

        $credentials = new Credentials(
            $this->container->getParameter('wandi_oauth.resource_owners.'.$lowerResourceOwnerName.'.client_id'),
            $this->container->getParameter('wandi_oauth.resource_owners.'.$lowerResourceOwnerName.'.client_secret'),
            $callbackRoute ? $this->container->get('router')->generate($callbackRoute, [], UrlGenerator::ABSOLUTE_URL) : null
        );

        $scopes = $this->container->getParameter('wandi_oauth.resource_owners.'.$lowerResourceOwnerName.'.scopes');
        $baseApiUrl = $this->container->getParameter('wandi_oauth.resource_owners.'.$lowerResourceOwnerName.'.base_api_url');
        $apiVersion = $this->container->getParameter('wandi_oauth.resource_owners.'.$lowerResourceOwnerName.'.api_version');

        $this->factory->setHttpClient(new CurlClient());
        $service = $this->factory->createService($resourceOwnerName, $credentials, $this->storage, $scopes, $baseApiUrl, $apiVersion);

        if (!$this->storage->hasAccessToken($resourceOwnerName)) {
            $this->storage->storeAccessToken($resourceOwnerName, in_array($resourceOwnerName, ResourceOwners::$oauth1) ? new StdOAuth1Token() : new StdOAuth2Token());
        }

        return $this->serviceCache[$resourceOwnerName] = $service;
    }
}
 