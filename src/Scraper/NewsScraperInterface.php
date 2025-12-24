<?php

namespace App\Scraper;

interface NewsScraperInterface {

    /**
     * Performs the extraction process
     * @return array An array of objects App\Entity\Feed
     */
    public function scrape(): array;

    /**
     * @return string Newspaper identifier (e.g. 'El Mundo')
     */
    public function getName(): string;
}
