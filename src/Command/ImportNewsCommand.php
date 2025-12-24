<?php

namespace App\Command;

use App\Service\FeedManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-news'
)]
class ImportNewsCommand extends Command
{

    private FeedManager $feedManager;
    private iterable $scrapers;

    public function __construct(
        FeedManager $feedManager,
        #[Symfony\Component\DependencyInjection\Attribute\TaggedIterator('app.news_scraper')]
        iterable $scrapers)
    {
        parent::__construct();
        $this->feedManager = $feedManager;
        $this->scrapers = $scrapers;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $totalImported = 0;

        try {
            foreach ($this->scrapers as $scraper) {
                $io->section('Scraping: ' . $scraper->getName());
                $count = $this->feedManager->importFeeds($scraper);
                $io->note(sprintf('Done: %d new items.', $count));
                $totalImported += $count;
            }

            $io->success(sprintf('Process completed. Total new: %d', $totalImported));
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('An error occurred during the import: ', $e->getMessage());
            return Command::FAILURE;
        }
    }
}
