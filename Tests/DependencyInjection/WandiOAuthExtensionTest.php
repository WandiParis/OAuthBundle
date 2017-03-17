<?php


namespace Wandi\OAuthBundle\Tests\DependencyInjection;

use Wandi\OAuthBundle\DependencyInjection\WandiOAuthExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class WandiOAuthExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var WandiOAuthExtension
     */
    private $extension;

    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    private $config = array(
        0 => array(
            'resource_owners' => array(
                'xing' => array(
                    'client_id' => 'thisismyclientid',
                    'client_secret' => 'thisismyclientsecret',
                ),
                'facebook' => array(
                    'client_id' => 'thisismyotherclientid',
                    'client_secret' => 'thisismyotherclientsecret',
                )
            )
        )
    );

    public function setUp()
    {
        $this->extension = new WandiOAuthExtension();
        $this->containerBuilder = new ContainerBuilder();
    }

    public function testExtensionHasCorrectAlias()
    {
        $this->assertEquals('wandi_oauth', $this->extension->getAlias());
    }

    public function testServicesAreCreated()
    {
        $this->extension->load($this->config, $this->containerBuilder);

        $definitions = $this->containerBuilder->getDefinitions();
        $this->assertEquals(6, count($definitions));

        $this->assertTrue($this->containerBuilder->hasDefinition('wandi_oauth.service.xing'));
        $this->assertTrue($this->containerBuilder->hasDefinition('wandi_oauth.service.facebook'));
    }

    public function testParametersAreCreated()
    {
        $this->extension->load($this->config, $this->containerBuilder);

        $parameters = $this->containerBuilder->getParameterBag();
        $this->assertEquals(44, count($parameters->all()));

        $this->assertTrue($parameters->has('wandi_oauth.resource_owners'));

        $this->assertTrue($parameters->has('wandi_oauth.resource_owners.xing.client_id'));
        $this->assertTrue($parameters->has('wandi_oauth.resource_owners.xing.client_secret'));
        $this->assertTrue($parameters->has('wandi_oauth.resource_owners.xing.callback_url'));

        $this->assertTrue($parameters->has('wandi_oauth.resource_owners.facebook.client_id'));
        $this->assertTrue($parameters->has('wandi_oauth.resource_owners.facebook.client_secret'));
        $this->assertTrue($parameters->has('wandi_oauth.resource_owners.facebook.callback_url'));
    }

}
 