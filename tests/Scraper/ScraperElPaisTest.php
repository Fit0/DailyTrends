<?php

declare(strict_types=1);

namespace App\Tests\Scraper;

use App\Scraper\ScraperElPais;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

#[CoversClass(ScraperElPais::class)]
class ScraperElPaisTest extends TestCase
{
    public function testScrapeExtractsNewsCorrectly(): void
    {
        $htmlFixture = <<<HTML
        <html>
            <body>
                <article>
                    <h2><a href="/noticia1">Titular de Prueba 1</a></h2>
                    <p>Resumen de la noticia 1</p>
                </article>
                <article>
                    <h2><a href="/noticia2">Titular de Prueba 2</a></h2>
                     </article>
            </body>
        </html>
        HTML;

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->expects($this->once())
            ->method('getContent')->willReturn($htmlFixture);

        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->method('request')->willReturn($mockResponse);

        $scraper = new ScraperElPais($mockHttpClient);
        $newsItems = $scraper->scrape();

        $this->assertCount(2, $newsItems);
        $this->assertSame('Titular de Prueba 1', $newsItems[0]->getTitle());
        $this->assertSame('Without summary', $newsItems[1]->getBody());
        $this->assertSame('El País', $newsItems[0]->getSource());
    }
}
