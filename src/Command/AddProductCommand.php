<?php

namespace App\Command;

use App\Service\ProductService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AddProductCommand extends Command
{
    protected static $defaultName = 'app:add-product';

    /** @var ProductService */
    private $productService;

    /**
     * AddProductCommand constructor.
     * @param ProductService $productService
     */
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
        parent::__construct();
    }


    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('url', InputArgument::OPTIONAL, 'Product URL')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $url = $input->getArgument('url');
        $product = $this->productService->createProduct($url);

        $io->success(sprintf('Created product with id %d', $product->getId()));

    }
}
