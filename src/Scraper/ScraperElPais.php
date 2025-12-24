<?php

namespace App\Scraper;

use App\Entity\Feed;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ScraperElPais implements NewsScraperInterface
{

    private HttpClientInterface $client;
    private const URL = 'https://www.elpais.com';
    private const NEWSPAPER = 'El País';

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getName(): string
    {
        return self::NEWSPAPER;
    }

    public function scrape(): array
    {
        $url = self::URL;

        $response = $this->client->request('GET', $url, [
            'verify_peer' => false,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ]
        ]);

        $html = $response->getContent();
        $crawler = new Crawler($html);
        $news = [];

        $crawler->filter('article')->slice(0, 5)->each(function (Crawler $node) use (&$news){
            try {
                $title = $node->filter('h2')->text();
                $link = $node->filter('h2 a')->attr('href');

                $summary = $node->filter('p')->count() > 0 ? $node->filter('p')->text() : 'Without summary';

                $feed = new Feed();
                $feed->setTitle(trim($title));
                $feed->setBody($summary);
                $feed->setUrl($link);
                $feed->setSource($this->getName());

                $news[] = $feed;

            } catch (\Exception $e) {
                // If nothing is found, proceed to the next one
            }
        });

        return $news;
    }
}
