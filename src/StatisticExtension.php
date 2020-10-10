<?php

declare(strict_types=1);

namespace Mathematicator\Statistics;


use Nette\DI\CompilerExtension;

final class StatisticExtension extends CompilerExtension
{
	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('statisticsManager'))
			->setFactory(StatisticsManager::class);
	}
}
