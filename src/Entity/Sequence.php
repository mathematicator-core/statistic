<?php

declare(strict_types=1);

namespace Mathematicator\Statistics\Entity;


use Baraja\Doctrine\UUID\UuidIdentifier;
use Doctrine\ORM\Mapping as ORM;
use Nette\SmartObject;
use Nette\Utils\Strings;

/**
 * @ORM\Entity()
 */
class Sequence
{
	use UuidIdentifier;

	private const FORMAT_PATTERN = '/^\%(?<type>[a-zA-Z0-9]+)\s+(A\d+)\s?(?<content>.*?)\s*$/';

	/** @ORM\Column(type="string", unique=true) */
	private string $aId;

	/** @ORM\Column(type="text", nullable=true) */
	private ?string $sequence = null;

	/** @ORM\Column(type="text", nullable=true) */
	private ?string $data = null;


	public function __construct(string $aId)
	{
		$this->aId = $aId;
		$this->updateData();
	}


	public function getAId(): string
	{
		return $this->aId;
	}


	/**
	 * @return int[]
	 */
	public function getSequence(): array
	{
		if ($this->sequence === null) {
			return [];
		}

		$return = [];
		foreach (explode(',', $this->sequence) as $item) {
			$return[] = (int) trim($item);
		}

		return $return;
	}


	public function getData(): ?string
	{
		return $this->data;
	}


	public function getDataType(string $type): ?string
	{
		if ($this->getData() === null) {
			$this->updateData();
		}

		$return = [];

		foreach (explode("\n", (string) $this->getData()) as $line) {
			if (preg_match(self::FORMAT_PATTERN, $line, $parser) && $parser['type'] === $type) {
				$return[] = $parser['content'];
			}
		}
		if ($return === []) {
			return null;
		}

		$return = implode("\n", $return);

		return $type === 'A' ? str_replace('_', '', $return) : $return;
	}


	public function updateData(): void
	{
		if ($this->data === null) {
			$this->data = Strings::normalize(
				Strings::fixEncoding(
					(string) file_get_contents('https://oeis.org/search?q=id:' . $this->getAId() . '&fmt=text')
				)
			);
		}
	}


	public function getFormula(): ?string
	{
		return $this->getDataType('F');
	}
}
