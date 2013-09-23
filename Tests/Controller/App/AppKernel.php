<?php

namespace GeorgetteParty\BaseBundle\Tests\Controller\App;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Tests\Fixtures\BaseBundle\BaseBundle;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // Dependencies
            new FrameworkBundle(),
            //new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            //new Symfony\Bundle\MonologBundle\MonologBundle(),
            //new Symfony\Bundle\TwigBundle\TwigBundle(),
            //new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            //new JMS\SerializerBundle\JMSSerializerBundle($this),
            //new FOS\RestBundle\FOSRestBundle(),
            new BaseBundle()
        );

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        // We don't need that Environment stuff, just one config
        $loader->load(__DIR__.'/config.yml');
    }
}