<?php

declare(strict_types=1);

namespace Mathematicator\Statistics;


use Baraja\Doctrine\EntityManager;
use Baraja\Doctrine\EntityManagerException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Mathematicator\Statistics\Entity\Sequence;
use Nette\Utils\Strings;
use Nette\Utils\Validators;

final class StatisticsManager
{
	private EntityManager $entityManager;


	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}


	/**
	 * @return string[]
	 */
	public function getNumbers(string $query): array
	{
		$query = (string) preg_replace('/[^0-9-.\/]/', ';', $query);
		$query = (string) preg_replace('/\;+/', ';', $query);

		$return = [];
		foreach (explode(';', $query) as $number) {
			if (Validators::isNumeric($number)) {
				$return[] = $number;
			}
		}

		return $return;
	}


	/**
	 * @return float[][]
	 */
	public function getData(string $data): array
	{
		$return = [];
		foreach (explode("\n", Strings::normalize($data)) as $line) {
			$numbers = [];
			foreach ($this->getNumbers($line) as $number) {
				$numbers[] = (float) $number;
			}
			$return[] = $numbers;
		}

		return $return;
	}


	/**
	 * @param int[]|float[] $array
	 */
	public function getMedian(array $array): float|int
	{
		if ($array) {
			$count = \count($array);
			sort($array);
			$mid = (int) floor(($count - 1) / 2);

			return ($array[$mid] + $array[$mid + 1 - $count % 2]) / 2;
		}

		return 0;
	}


	/**
	 * @param int[]|float[] $array
	 */
	public function getAverage(array $array): float|int
	{
		if ($array) {
			$sum = 0;
			$count = 0;
			foreach ($array as $item) {
				$count++;
				$sum += $item;
			}

			return $sum / ($count === 0 ? 1 : $count);
		}

		return 0;
	}


	/**
	 * @param string[] $sequence
	 * @return Sequence[]
	 */
	public function getSequences(array $sequence, int $limit = 6): array
	{
		assert(Validators::everyIs($sequence, 'string'));
		/** @var Sequence[] $return */
		$return = $this->entityManager->getRepository(Sequence::class)
			->createQueryBuilder('sequence')
			->where('sequence.sequence LIKE :sequence')
			->setParameter('sequence', implode(',', $sequence) . ',%')
			->setMaxResults($limit)
			->getQuery()
			->getResult();

		foreach ($return as $item) {
			if ($item->getData() === null) {
				$item->updateData();
				try {
					$this->entityManager->flush($item);
				} catch (EntityManagerException $e) {
				}
			}
		}

		return $return;
	}


	/**
	 * @throws EntityManagerException|NoResultException|NonUniqueResultException
	 */
	public function getSequence(string $aId): Sequence
	{
		/** @var Sequence $return */
		$return = $this->entityManager->getRepository(Sequence::class)
			->createQueryBuilder('sequence')
			->where('sequence.aId = :aId')
			->setParameter('aId', $aId)
			->setMaxResults(1)
			->getQuery()
			->getSingleResult();

		if ($return->getData() === null) {
			$return->updateData();
			$this->entityManager->flush($return);
		}

		return $return;
	}
}
