<?php

namespace App\Scraper;

use App\Entity\Feed;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ScraperElMundo implements NewsScraperInterface
{

    private HttpClientInterface $client;
    private const URL = 'https://www.elmundo.es';
    private const NEWSPAPER = 'El Mundo';

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

        //We make the request (including the SSL fix and a User-Agent)
        $response = $this->client->request('GET', $url, [
            'verify_peer' => false,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ]
        ]);

        $html = $response->getContent();
        $crawler = new Crawler($html);
        $news = [];

        //We attempt to retrieve the containers of the main news articles
        $crawler->filter('.ue-c-cover-content')->slice(0,5)->each(function (Crawler $node) use (&$news){
            try {
                $titleNode = $node->filter('.ue-c-cover-content__headline, h2')->first();
                $title = $titleNode->text();
                $link = $node->filter('a')->first()->attr('href');
                $summary = 'Without summary';
                $summaryNode = $node->filter('article p')->first();
                if ($summaryNode->count() > 0) {
                    $summary = trim($summaryNode->text());
                }

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
