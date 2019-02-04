<?php

namespace App\Command;

use App\Repository\ProductRepository;
use App\Service\ProductService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateProductCommand extends Command
{
	protected static $defaultName = 'app:update-product';

	/** @var ProductService */
	private $productService;

	/** @var ProductRepository */
	private $repository;

	public function __construct(ProductService $productService, ProductRepository $repository)
	{
		$this->productService = $productService;
		$this->repository = $repository;
		parent::__construct();
	}


	protected function configure()
	{
		$this
			->setDescription('Add a short description for your command')
			->addArgument('id', InputArgument::OPTIONAL, 'Product ID');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$startTime = microtime(true);
		$maxExecutionTime = ini_get('max_execution_time');

		$io = new SymfonyStyle($input, $output);
		$id = $input->getArgument('id');
		if ($id) {
			$product = $this->repository->find($id);
			$this->productService->updateProduct($product);
			$io->success(sprintf('Product price has been updated to %.2f €', $product->getCurrentPrice()));
		} else {
			$io->note(sprintf('Maximum execution time is set to: %f', $maxExecutionTime));

			$products = $this->productService->fetchProductsNeedingUpdate();
			$updated = 0;
			$updateMessages = [];

			$io->progressStart(count($products));

			foreach ($products as $product) {
				$this->productService->updateProduct($product);
				$updateMessages[] = sprintf('Product %d price has been updated to %.2f €', $product->getId(), $product->getCurrentPrice());
				$io->progressAdvance();
				$updated++;

				if (($maxExecutionTime > 0 && microtime(true) - $startTime) > $maxExecutionTime) {
					$io->text('Maximum execution time has been reached, aborting now...');
					break;
				}
			}

			$io->newLine(2);
			$io->listing($updateMessages);
			$remaining = $this->productService->countProductsNeedingUpdate();
			$io->success(sprintf('Updated %d products. Remaining: %d', $updated, $remaining));

		}
	}
}
