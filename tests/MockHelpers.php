<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie\MessageBus;

trait MockHelpers
{
	/**
	 * @param string $class
	 * @param callable|null $init
	 *
	 * @return mixed
	 */
	private function mock(string $class, callable $init = null)
	{
		/* @var $this \PHPUnit\Framework\TestCase */

		$container = $this->prophesize($class);

		if ($init) {
			$init($container);
		}

		return $container->reveal();
	}
}
