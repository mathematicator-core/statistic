<?php

declare(strict_types=1);

namespace Mathematicator\Statistics\Test;


use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../Bootstrap.php';

class StatisticsManagerTest extends TestCase
{
	public function testSample(): void
	{
		Assert::same('1', '1');
	}
}

(new StatisticsManagerTest())->run();
