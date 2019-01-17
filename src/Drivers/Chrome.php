<?php

namespace duncan3dc\Laravel\Drivers;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Laravel\Dusk\SupportsChrome;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;

class Chrome implements DriverInterface
{
    use SupportsChrome;

    private static $afterClass;


    /**
     * Create a new instance and automatically start the driver.
     */
    public function __construct()
    {
        static::startChromeDriver();
    }


    /**
     * {@inheritDoc}
     */
    public function getDriver()
    {
            $options = (new ChromeOptions)->addArguments([
        '--headless',
        '--disable-gpu',
        '--no-sandbox',
        '--ignore-certificate-errors',
    ]);
        $cap = DesiredCapabilities::chrome();
        $cap->setCapability(ChromeOptions::CAPABILITY, $options);
        $cap->setCapability(WebDriverCapabilityType::ACCEPT_SSL_CERTS, true);
        $cap->setCapability('acceptInsecureCerts', true);
        return RemoteWebDriver::create("http://localhost:9515", $cap);
    }


    /**
     * Required for upstream compatibility.
     */
    protected static function afterClass($handler)
    {
        self::$afterClass = $handler;
    }


    /**
     * Ensure the driver is closed by the upstream library.
     */
    public function __destruct()
    {
        $handler = self::$afterClass;
        $handler();
    }
}
