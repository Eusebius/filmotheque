<?php

namespace Eusebius\Filmotheque;

use PHPUnit_Framework_TestCase,
    WebDriverCapabilityType,
    RemoteWebDriver;

/**
 * Description of newSeleneseTest
 *
 * @author Eusebius <eusebius@eusebius.fr>
 */
class FakeSeleneseTest extends PHPUnit_Framework_TestCase {

    /**
     * @var \RemoteWebDriver
     */
    protected $webDriver;

    public function setUp() {
        $capabilities = array(WebDriverCapabilityType::BROWSER_NAME => 'firefox');
        $this->webDriver = RemoteWebDriver::create('http://localhost:4444/wd/hub', $capabilities);
    }

    public function tearDown() {
        $this->webDriver->close();
    }

    protected $url = 'http://www.netbeans.org/';

    public function testSimple() {
        $this->webDriver->get($this->url);
        // checking that page title contains word 'NetBeans'
        $this->assertContains('NetBeans', $this->webDriver->getTitle());
    }

}
