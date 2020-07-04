<?php

use Dephpug\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    protected $defaultConfig;

    /**
     * @before
     */
    protected function _before()
    {
        $config = new Config();
        $this->defaultConfig = $config->getConfig();
    }

    // tests
    public function testReplaceOptionsWithNewValues()
    {
        $config = $this->getMockBuilder(Config::class)
            ->setMethods(['getConfigFromFile'])
            ->getMock();

        $config->method('getConfigFromFile')
            ->willReturn(['server' => ['port' => 123]]);

        $config->configure();
        $configuration = $config->getConfig();
        $this->assertEquals(123, $config->server['port']);
    }

    public function testKeepKeysNotReplaced()
    {
        $config = $this->getMockBuilder(Config::class)
            ->setMethods(['getConfigFromFile'])
            ->getMock();

        $config->method('getConfigFromFile')
            ->willReturn(['server' => ['port' => 123]]);

        $config->configure();
        $this->assertEquals('0.0.0.0', $config->server['host']);
    }

    public function testReplaceOptionsWithDataFromYaml()
    {
        $config = $this->getMockBuilder(Config::class)
            ->setMethods(['getPathFile'])
            ->getMock();

        $config->method('getPathFile')
            ->willReturn(__DIR__.'/../data/configValid.yml');

        $config->configure();
        $this->assertEquals(4005, $config->debugger['port']);
    }

    public function testExceptionIfYamlIsInvalid()
    {
        $this->expectException(\Symfony\Component\Yaml\Exception\ParseException::class);
        $config = $this->getMockBuilder(Config::class)
            ->setMethods(['getPathFile'])
            ->getMock();

        $config->method('getPathFile')
            ->willReturn(__DIR__.'/../data/configInvalid.yml');
        $config->getConfigFromFile();
    }

    public function testMagicMethodGettingUnexistKey()
    {
        $config = new Config();
        $this->assertNull($config->notExistKey);
    }

    public function testMagicMethodGettingExistKey()
    {
        $config = new Config();
        $config->configure();
        $this->assertEquals($config->getConfig()['server']['port'], $config->server['port']);
    }
}
