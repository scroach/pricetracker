<?php


namespace App\Service;


class CrawlerResponse
{
	/** @var null|string */
	private $requestUrl = null;
	/** @var null|string */
	private $responseBody = null;

	public function __construct(?string $requestUrl, ?string $responseBody)
	{
		$this->requestUrl = $requestUrl;
		$this->responseBody = $responseBody;
	}

	public function getRequestUrl(): ?string
	{
		return $this->requestUrl;
	}

	public function setRequestUrl(?string $requestUrl): void
	{
		$this->requestUrl = $requestUrl;
	}

	public function getResponseBody(): ?string
	{
		return $this->responseBody;
	}

	public function setResponseBody(?string $responseBody): void
	{
		$this->responseBody = $responseBody;
	}


}