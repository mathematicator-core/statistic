<?php

declare(strict_types=1);

namespace Mathematicator\Tokenizer\Test;


use App\Booting;
use Mathematicator\Engine\Tests\Bootstrap;
use Mathematicator\Engine\Tests\NumberRewriterTest;
use Mathematicator\NumberRewriter;
use Mathematicator\Tokenizer\Tokenizer;
use Nette\DI\Container;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../bootstrap.php';

class StatisticsManagerTest extends TestCase
{

	public function testSample(): void
	{
		Assert::same('1', '1');
	}

}

(new StatisticsManagerTest())->run();
