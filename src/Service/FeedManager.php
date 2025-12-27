<?php

declare(strict_types=1);

namespace App\Service;

use App\Scraper\NewsScraperInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Feed;

class FeedManager
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function importFeeds(NewsScraperInterface $scraper): int
    {
        $feeds = $scraper->scrape();

        if (empty($feeds)) {
            return 0;
        }

        $scrapedUrls = [];
        foreach ($feeds as $feed) {
            $scrapedUrls[] = $feed->getUrl();
        }

        $existingEntities = $this->entityManager->getRepository(Feed::class)
        ->findBy(['url' => $scrapedUrls]);

        $existingUrlsMap = [];
        foreach ($existingEntities as $existingFeed) {
            $existingUrlsMap[$existingFeed->getUrl()] = true;
        }

        $count = 0;

        foreach($feeds as $feed) {
            if (!isset($existingUrlsMap[$feed->getUrl()])) {
                $this->entityManager->persist($feed);
                $count++;
            }
        }

        $this->entityManager->flush();

        return $count;
    }
}
