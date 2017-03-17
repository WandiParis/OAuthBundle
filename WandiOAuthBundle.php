<?php

namespace Wandi\OAuthBundle;

use Wandi\OAuthBundle\DependencyInjection\WandiOAuthExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class WandiOAuthBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            return new WandiOAuthExtension();
        }

        return $this->extension;
    }
}
