<?php

require 'pages/DownloadWithWget.php';

class DownloadWithWgetTest extends \PHPUnit_Framework_TestCase {

    /**
     * The object under test.
     *
     * @var object
     */
    private $instance;

    /**
     * Sets up the fixture.
     *
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp () {
        $this->instance = $this->getObjectForTrait('DownloadWithWget');
    }

    /**
     * Tests getFileContents method
     */
    public function testGetFileContents () {
        $this->assertContains(
            "* <----- vous &ecirc;tes ici",
            $this->instance->getFileContents("http://www.perdu.com")
        );
    }
}
