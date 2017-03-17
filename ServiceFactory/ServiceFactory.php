<?php

namespace Wandi\OAuthBundle\ServiceFactory;

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Exception\Exception;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\ServiceFactory as BaseServiceFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
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
            throw new Exception('Resource owner ' . $resourceOwnerName . ' is not available');
        }

        if (isset($this->serviceCache[$resourceOwnerName])) {
            return $this->serviceCache[$resourceOwnerName];
        }

        $lowerResourceOwnerName = $string = strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/',"_$1", $resourceOwnerName));

        $credentials = new Credentials(
            $this->container->getParameter('wandi_oauth.resource_owners.' . $lowerResourceOwnerName . '.client_id'),
            $this->container->getParameter('wandi_oauth.resource_owners.' . $lowerResourceOwnerName . '.client_secret'),
            $this->container->get('router')->generate($this->container->getParameter('wandi_oauth.resource_owners.' . $lowerResourceOwnerName . '.callback_route'), [], UrlGeneratorInterface::ABSOLUTE_URL)
        );

        $scopes = $this->container->getParameter('wandi_oauth.resource_owners.' . $lowerResourceOwnerName . '.scopes');
        $baseApiUrl = $this->container->getParameter('wandi_oauth.resource_owners.' . $lowerResourceOwnerName . '.base_api_url');
        $apiVersion = $this->container->getParameter('wandi_oauth.resource_owners.' . $lowerResourceOwnerName . '.api_version');

        return $this->serviceCache[$resourceOwnerName] = $this->factory->createService($resourceOwnerName, $credentials, $this->storage, $scopes, $baseApiUrl, $apiVersion);
    }
}
 