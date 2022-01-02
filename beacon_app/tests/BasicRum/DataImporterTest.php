<?php

namespace  App\Tests\BasicRum;

use PHPUnit\Framework\TestCase;

use App\BasicRum\DataImporter;

use donatj\MockWebServer\MockWebServer;
use donatj\MockWebServer\Response;

class DataImporterTest extends TestCase
{

    /** @var MockWebServer */
	protected static $server;

	public static function setUpBeforeClass() : void {
		self::$server = new MockWebServer(8444);
		self::$server->start();
		self::$server->setResponseOfPath('/', new Response('Ok.' . PHP_EOL));
	}

	static function tearDownAfterClass() : void {
		// stopping the web server during tear down allows us to reuse the port for later tests
		self::$server->stop();
	}

    public function testSanity()
    {
        $dataImporter = new DataImporter();

        var_dump(self::$server->getLastRequest());


    }

}
