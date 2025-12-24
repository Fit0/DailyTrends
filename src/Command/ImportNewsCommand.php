<?php

namespace App\Command;

use App\Scraper\ScraperElMundo;
use App\Service\FeedManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-news',
    description: 'Import news from El Mundo`s scraper',
)]
class ImportNewsCommand extends Command
{

    private FeedManager $feedManager;
    private ScraperElMundo $scraper;

    public function __construct(FeedManager $feedManager, ScraperElMundo $scraper)
    {
        parent::__construct();
        $this->feedManager = $feedManager;
        $this->scraper = $scraper;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Starting news import: ' . $this->scraper->getName());

        try {
            $importedCount = $this->feedManager->importFeeds($this->scraper);

            if ($importedCount > 0) {
                $io->success(sprintf('It has successfully imported %d news items.', $importedCount));
            } else {
                $io->info('No new items were found to import.');
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('An error occurred during the import: ', $e->getMessage());
            return Command::FAILURE;
        }
    }
}
