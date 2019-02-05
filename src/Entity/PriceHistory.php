<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PriceHistoryRepository")
 */
class PriceHistory
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="float")
	 */
	private $value;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $currency;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $timestamp;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\RequestLog")
	 */
	private $request;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getValue(): ?float
	{
		return $this->value;
	}

	public function setValue(float $value): self
	{
		$this->value = $value;

		return $this;
	}

	public function getTimestamp(): ?\DateTimeInterface
	{
		return $this->timestamp;
	}

	public function setTimestamp(\DateTimeInterface $timestamp): self
	{
		$this->timestamp = $timestamp;

		return $this;
	}

	public function getRequest(): ?RequestLog
	{
		return $this->request;
	}

	public function setRequest(?RequestLog $request): self
	{
		$this->request = $request;

		return $this;
	}

	public function getCurrency(): ?string
	{
		return $this->currency;
	}

	public function setCurrency(?string $currency): void
	{
		$this->currency = $currency;
	}

}
