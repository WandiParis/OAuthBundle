<?php


namespace Wandi\OAuthBundle\Tests\ServiceFactory;


use Wandi\OAuthBundle\DependencyInjection\WandiOAuthExtension;
use Wandi\OAuthBundle\ServiceFactory\ServiceFactory;
use OAuth\Common\Storage\Memory;
use OAuth\OAuth1\Service\Xing;
use OAuth\ServiceFactory as BaseServiceFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceFactory
     */
    private $serviceFactory;

    private $config = array(
        0 => array(
            'resource_owners' => array(
                'xing' => array(
                    'client_id' => 'thisismyclientid',
                    'client_secret' => 'thisismyclientsecret',
                    'callback_route' => 'thisismycallbackroute',
                ),
            )
        )
    );

    public function setUp()
    {
        $containerBuilder = new ContainerBuilder();
        $extension = new WandiOAuthExtension();
        $extension->load($this->config, $containerBuilder);

        $this->serviceFactory = new ServiceFactory($containerBuilder, new BaseServiceFactory(), new Memory());
    }

    public function testCreateServiceReturnsCorrectService()
    {
        $service = $this->serviceFactory->createService('Xing');

        $this->assertTrue($service instanceof Xing);
    }

    public function testCreateServiceThrowsExceptionIfResourceOwnerNotFound()
    {
        $this->setExpectedException('\OAuth\Common\Exception\Exception');

        $this->serviceFactory->createService('not_existing_service');

        $this->fail('exception not thrown');
    }
}
 