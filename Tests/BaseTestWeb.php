<?php

namespace GeorgetteParty\BaseBundle\Tests;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DependencyInjection\Container;

class BaseTestWeb extends WebTestCase
{
    protected $client;
    protected $container;

    /**
     * @return Client
     */
    public function getClient()
    {
        if (!$this->client) {
            $this->client = static::createClient();
        }
        return $this->client;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        if (!$this->container) {
            $this->container = $this->getClient()->getContainer();
        }
        return $this->container;
    }

    public function assertException($function, $parameters = array(), $expectedExceptionClass = '', $expectedMessage = '')
    {
        $e = null;
        $hasException = false;

        try {
            call_user_func_array($function, $parameters);
        } catch (\Exception $e) {
            $hasException = true;
        }
        $this->assertEquals(true, $hasException, 'An expected excpetion has not been thrown !');

        if ($expectedExceptionClass) {
            $this->assertEquals($expectedExceptionClass, get_class($e), 'An exception has been thrown, but not from the expected type');
        }
        if ($expectedMessage) {
            $this->assertEquals($expectedMessage, $e->getMessage(), 'An expected excpetion has been thrown, but message is invalid');
        }
    }
}