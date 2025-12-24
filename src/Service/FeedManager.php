<?php

namespace App\Service;

use App\Scraper\NewsScraperInterface;
use Doctrine\ORM\EntityManagerInterface;

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
        $count = 0;

        foreach($feeds as $feed) {
            $existing = $this->entityManager->getRepository($feed::class)
            ->findOneBy(['url' => $feed->getUrl()]);

            if (!$existing) {
                $this->entityManager->persist($feed);
                $count++;
            }
        }

        $this->entityManager->flush();

        return $count;
    }
}
