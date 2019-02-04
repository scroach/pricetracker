<?php


namespace App\Service;


class PriceGuess
{
	/** @var null|float */
	private $price = null;

	/** @var int */
	private $confidenceLevel = 1;

	public function __construct(?float $price, int $confidenceLevel = 1)
	{
		$this->price = $price;
		$this->confidenceLevel = $confidenceLevel;
	}

	public function getPrice(): ?float
	{
		return $this->price;
	}

	public function setPrice(?float $price): void
	{
		$this->price = $price;
	}

	public function getConfidenceLevel(): int
	{
		return $this->confidenceLevel;
	}

	public function setConfidenceLevel(int $confidenceLevel): void
	{
		$this->confidenceLevel = $confidenceLevel;
	}

}