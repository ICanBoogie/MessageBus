<?php

namespace ICanBoogie\MessageBus;

trait MockHelpers
{
	/**
	 * @param string $class
	 * @param callable|null $init
	 *
	 * @return mixed
	 */
	private function mock($class, callable $init = null)
	{
		/* @var $this \PHPUnit_Framework_TestCase */

		$container = $this->prophesize($class);

		if ($init) {
			$init($container);
		}

		return $container->reveal();
	}
}
