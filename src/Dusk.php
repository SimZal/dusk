<?php

namespace duncan3dc\Laravel;

use duncan3dc\Laravel\Drivers\Chrome;
use duncan3dc\Laravel\Drivers\DriverInterface;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Laravel\Dusk\Browser;

class Dusk
{
    /**
     * @var Browser $browser The browser instance to use.
     */
    private $browser;

    private $dir;

    /**
     * Create a new instance.
     *
     * @param DriverInterface $driver The browser driver to use
     */
    public function __construct(DriverInterface $driver = null)
    {
        if ($driver === null) {
            $driver =  new Chrome;
        }

        $this->browser = new Browser($driver->getDriver());
        $this->driver = $driver;
    }


    /**
     * Proxy any methods to the internal browser instance.
     *
     * @param string $function The method name to call
     * @param array $args The parameters to pass to the method
     *
     * @return mixed
     */
    public function __call($function, $args)
    {
        $result = $this->browser->$function(...$args);

        if ($result instanceof Browser) {
            return $this;
        }

        $result = Element::convertElement($result);

        if (is_array($result)) {
            $result = array_map([Element::class, "convertElement"], $result);
        }

        return $result;
    }


    /**
     * Get the internal Browser instance in use.
     *
     * @return Browser
     */
    public function getBrowser()
    {
        return $this->browser;
    }


    /**
     * Get the internal web driver instance in use.
     *
     * @return RemoteWebDriver
     */
    public function getDriver()
    {
        return $this->browser->driver;
    }


    /**
     * Take a screenshot and store it on disk.
     *
     * @param string $filename The filename to store (no extension)
     *
     * @return $this
     */
    public function screenshot($filename)
    {
        $this->getDriver()->takeScreenshot($this->dir . "/{$filename}.png");

        return $this;
    }

    public function screenshotDir($dir = null)
    {
        if( empty($dir) ){
            return $this->dir;
        }
        $this->dir = $dir;

        return $this;
    }


    /**
     * Ensure the browser is closed down after use.
     */
    public function __destruct()
    {
        $this->browser->quit();
        unset($this->browser);
    }
}
