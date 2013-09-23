<?php


namespace GeorgetteParty\BaseBundle\Tests\Controller;

use GeorgetteParty\BaseBundle\Controller\BaseController;
use GeorgetteParty\BaseBundle\Tests\Controller\App\AppKernel;
use Symfony\Component\Translation\TranslatorInterface;


/**
 * BaseControllerTest
 *
 */
class BaseControllerTest extends \PHPUnit_Framework_TestCase
{
    protected $container;

    // TODO preExecute hook test

    /**
     * Test if object return by getTranslator is an instance of TranslatorInterface
     */
    public function testGetTranslator()
    {
        $translator = $this->getController()->__getTranslator();
        $this->assertTrue($translator instanceof TranslatorInterface);
    }

    protected function getContainer()
    {
        if (!$this->container) {
            $kernel = new AppKernel('test', true);
            $kernel->registerBundles();
            $kernel->boot();
            $this->container = $kernel->getContainer();
        }
        return $this->container;
    }

    /**
     * @return ControllerTest
     */
    protected function getController()
    {
        $controller = new ControllerTest();
        $controller->setContainer($this->getContainer());

        return $controller;
    }
}

/**
 * ControllerTest
 * Mock class to test BaseController. Expose protected methods
 */
class ControllerTest extends BaseController
{
    public function __getTranslator()
    {
        return $this->getTranslator();
    }
}
