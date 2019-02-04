<?php

namespace App\Service\Crawler;


abstract class AbstractCrawler implements PriceCrawler
{
	protected abstract function getApplicableUrlRegex(): ?string;

	public function getConfidenceLevelByUrl(string $requestUrl)
	{
		$matches = preg_match($this->getApplicableUrlRegex(), $requestUrl);
		if ($matches === false) {
			throw new \Exception(sprintf('there seems to be a problem with the provided regex: %s', $this->getApplicableUrlRegex()));
		}
		return $matches ? 100 : 0;
	}

}