<?php
// source: /home/jakub/mytester/tests/config.neon 

class Container_7a8261c63f extends Nette\DI\Container
{
	protected $meta = [
		'types' => [
			'Nette\Caching\Storages\IJournal' => [1 => ['cache.journal']],
			'Nette\Caching\IStorage' => [1 => ['cache.storage']],
			'Tracy\ILogger' => [1 => ['tracy.logger']],
			'Tracy\BlueScreen' => [1 => ['tracy.blueScreen']],
			'Tracy\Bar' => [1 => ['tracy.bar']],
			'MyTester\Bridges\NetteDI\TestsRunner' => [1 => ['mytester.runner']],
			'MyTester\TestCase' => [
				1 => [
					'mytester.test.1',
					'mytester.test.2',
					'mytester.test.3',
					'mytester.test.4',
				],
			],
			'MyTester\Tests\TestCaseTest' => [1 => ['mytester.test.1']],
			'MyTester\Tests\EnvironmentTest' => [1 => ['mytester.test.2']],
			'MyTester\Tests\JobTest' => [1 => ['mytester.test.3']],
			'MyTester\Tests\AssertTest' => [1 => ['mytester.test.4']],
			'Nette\DI\Container' => [1 => ['container']],
		],
		'services' => [
			'cache.journal' => 'Nette\Caching\Storages\IJournal',
			'cache.storage' => 'Nette\Caching\IStorage',
			'container' => 'Nette\DI\Container',
			'mytester.runner' => 'MyTester\Bridges\NetteDI\TestsRunner',
			'mytester.test.1' => 'MyTester\Tests\TestCaseTest',
			'mytester.test.2' => 'MyTester\Tests\EnvironmentTest',
			'mytester.test.3' => 'MyTester\Tests\JobTest',
			'mytester.test.4' => 'MyTester\Tests\AssertTest',
			'tracy.bar' => 'Tracy\Bar',
			'tracy.blueScreen' => 'Tracy\BlueScreen',
			'tracy.logger' => 'Tracy\ILogger',
		],
		'tags' => [
			'mytester.test' => [
				'mytester.test.1' => TRUE,
				'mytester.test.2' => TRUE,
				'mytester.test.3' => TRUE,
				'mytester.test.4' => TRUE,
			],
		],
		'aliases' => [
			'cacheStorage' => 'cache.storage',
			'nette.cacheJournal' => 'cache.journal',
		],
	];


	public function __construct()
	{
		parent::__construct([
			'appDir' => '/home/jakub/mytester/tests',
			'wwwDir' => '/home/jakub/mytester/tests',
			'debugMode' => FALSE,
			'productionMode' => TRUE,
			'consoleMode' => TRUE,
			'tempDir' => '/home/jakub/mytester/tests/temp',
		]);
	}


	/**
	 * @return Nette\Caching\Storages\IJournal
	 */
	public function createServiceCache__journal()
	{
		$service = new Nette\Caching\Storages\SQLiteJournal('/home/jakub/mytester/tests/temp/cache/journal.s3db');
		return $service;
	}


	/**
	 * @return Nette\Caching\IStorage
	 */
	public function createServiceCache__storage()
	{
		$service = new Nette\Caching\Storages\FileStorage('/home/jakub/mytester/tests/temp/cache',
			$this->getService('cache.journal'));
		return $service;
	}


	/**
	 * @return Nette\DI\Container
	 */
	public function createServiceContainer()
	{
		return $this;
	}


	/**
	 * @return MyTester\Bridges\NetteDI\TestsRunner
	 */
	public function createServiceMytester__runner()
	{
		$service = new MyTester\Bridges\NetteDI\TestsRunner;
		return $service;
	}


	/**
	 * @return MyTester\Tests\TestCaseTest
	 */
	public function createServiceMytester__test__1()
	{
		$service = new MyTester\Tests\TestCaseTest;
		return $service;
	}


	/**
	 * @return MyTester\Tests\EnvironmentTest
	 */
	public function createServiceMytester__test__2()
	{
		$service = new MyTester\Tests\EnvironmentTest;
		return $service;
	}


	/**
	 * @return MyTester\Tests\JobTest
	 */
	public function createServiceMytester__test__3()
	{
		$service = new MyTester\Tests\JobTest;
		return $service;
	}


	/**
	 * @return MyTester\Tests\AssertTest
	 */
	public function createServiceMytester__test__4()
	{
		$service = new MyTester\Tests\AssertTest;
		return $service;
	}


	/**
	 * @return Tracy\Bar
	 */
	public function createServiceTracy__bar()
	{
		$service = Tracy\Debugger::getBar();
		if (!$service instanceof Tracy\Bar) {
			throw new Nette\UnexpectedValueException('Unable to create service \'tracy.bar\', value returned by factory is not Tracy\Bar type.');
		}
		return $service;
	}


	/**
	 * @return Tracy\BlueScreen
	 */
	public function createServiceTracy__blueScreen()
	{
		$service = Tracy\Debugger::getBlueScreen();
		if (!$service instanceof Tracy\BlueScreen) {
			throw new Nette\UnexpectedValueException('Unable to create service \'tracy.blueScreen\', value returned by factory is not Tracy\BlueScreen type.');
		}
		return $service;
	}


	/**
	 * @return Tracy\ILogger
	 */
	public function createServiceTracy__logger()
	{
		$service = Tracy\Debugger::getLogger();
		if (!$service instanceof Tracy\ILogger) {
			throw new Nette\UnexpectedValueException('Unable to create service \'tracy.logger\', value returned by factory is not Tracy\ILogger type.');
		}
		return $service;
	}


	public function initialize()
	{
		Tracy\Debugger::setLogger($this->getService('tracy.logger'));
		$runner = $this->getService('mytester.runner');
		spl_autoload_extensions(spl_autoload_extensions() . ",.phpt");
		MyTester\Bridges\NetteDI\TestsRunner::$autoloader = [
			[
				'MyTester\Tests\TestCaseTest',
				'/home/jakub/mytester/tests/../tests/TestCase.phpt',
			],
			[
				'MyTester\Tests\EnvironmentTest',
				'/home/jakub/mytester/tests/../tests/Environment.phpt',
			],
			[
				'MyTester\Tests\JobTest',
				'/home/jakub/mytester/tests/../tests/Job.phpt',
			],
			[
				'MyTester\Tests\AssertTest',
				'/home/jakub/mytester/tests/../tests/Assert.phpt',
			],
		];
		spl_autoload_register('MyTester\Bridges\NetteDI\TestsRunner::autoload');
		$runner->addSuit($this->getService('mytester.test.1'));
		$runner->addSuit($this->getService('mytester.test.2'));
		$runner->addSuit($this->getService('mytester.test.3'));
		$runner->addSuit($this->getService('mytester.test.4'));
		$runner->onExecute[] = ['MyTester\Environment', 'setup'];
		$runner->onExecute[] = ['MyTester\Environment', 'printInfo'];
	}

}
