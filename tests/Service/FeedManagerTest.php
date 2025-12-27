<?php

namespace App\Tests\Service;

use App\Entity\Feed;
use App\Repository\FeedRepository;
use App\Scraper\NewsScraperInterface;
use App\Service\FeedManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class FeedManagerTest extends TestCase
{
    private $entityManager;
    private $feedRepository;
    private $newsScraper;
    private $feedManager;

    protected function setUp(): void
    {
       $this->entityManager = $this->createMock(EntityManagerInterface::class);
       $this->feedRepository = $this->createMock(FeedRepository::class);

       $this->entityManager->method('getRepository')
           ->with(Feed::class)
           ->willReturn($this->feedRepository);

       $this->newsScraper = $this->createMock(NewsScraperInterface::class);

       $this->feedManager = new FeedManager($this->entityManager);
    }


    /**
     * Case 1: The scraper finds no news items.
     * Verify that the database is not called unnecessarily when the scraper returns an empty list.
     */
    public function testImportFeedsDoNothingIfScraperReturnsEmpty(): void
    {
        $this->newsScraper->method('scrape')->willReturn([]);

        // Expectation: 'persist' and 'flush' must NEVER be called
        $this->entityManager->expects($this->never())->method('persist');
        $this->entityManager->expects($this->never())->method('flush');

        $count = $this->feedManager->importFeeds($this->newsScraper);

        $this->assertEquals(0, $count);
    }

    /**
     * Case 2: Mix of new and existing news items.
     * Simulate that the scraper retrieves 2 items, but 1 already exists in the database.
     * The system must persist only the new item.
     */
    public function testImportFeedsFiltersDuplicatesAndPersistsOnlyNew(): void
    {
        $newFeed = (new Feed())->setUrl('http://example.com/new-news')->setTitle('New News');
        $existingFeed = (new Feed())->setUrl('http://example.com/existing-news')->setTitle('Existing News');

        $scrapedFeeds = [$newFeed, $existingFeed];

        $this->newsScraper->method('scrape')->willReturn($scrapedFeeds);

        // Simulate that the existing feed is already in the database
        $existingFeedInDb = (new Feed())->setUrl('http://example.com/existing-news')->setTitle('Existing News');

        $this->feedRepository->expects($this->once())
            ->method('findBy')
            ->with(['url' => ['http://example.com/new-news', 'http://example.com/existing-news']])
            ->willReturn([$existingFeedInDb]);

        // Expectation: 'persist' must be called only for the new feed
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($newFeed));

        $this->entityManager->expects($this->once())->method('flush');

        $count = $this->feedManager->importFeeds($this->newsScraper);

        $this->assertEquals(1, $count);
    }

    /**
     * Case 3: All scraped news items are new.
     * Verify that every item retrieved by the scraper is persisted when none exist in the database.
     */
    public function testImportFeedsPersistsAllIfNoneExist(): void
    {
        $feed1 = (new Feed())->setUrl('http://example.com/news1')->setTitle('News 1');
        $feed2 = (new Feed())->setUrl('http://example.com/news2')->setTitle('News 2');

        $scrapedFeeds = [$feed1, $feed2];

        $this->newsScraper->method('scrape')->willReturn($scrapedFeeds);

        // Simulate that no feeds exist in the database
        $this->feedRepository->method('findBy')->willReturn([]);

        // Expectation: 'persist' must be called for both feeds
        $this->entityManager->expects($this->exactly(2))->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        $count = $this->feedManager->importFeeds($this->newsScraper);

        $this->assertEquals(2, $count);
    }
}
