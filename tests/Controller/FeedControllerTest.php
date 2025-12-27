<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class FeedControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * Test GET /api/feeds
     * Verifies that the endpoint returns a successful JSON response.
     */
    public function testGetAllFeeds(): void
    {
        $this->client->request('GET', '/api/feeds');

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->assertTrue(
            $this->client->getResponse()->headers->contains('Content-Type', 'application/json'),
            'The response should be in JSON format'
        );
    }

    /**
     * Test GET /api/feeds/{id} with a non-existent ID.
     * Verifies that the ApiExceptionListener correctly handles 404 errors.
     */
    public function testGetNonExistentFeedReturnsJsonError(): void
    {
        $this->client->request('GET', '/api/feeds/999999');

        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('error', $data['status']);
        $this->assertEquals(404, $data['code']);
    }

    /**
     * Test the full CRUD cycle: Create -> Delete.
     */
    public function testCreateAndDeleteFeed(): void
    {
        // 1. Create a new Feed
        $payload = [
            'title' => 'Test News Title',
            'body' => 'This is a test body content.',
            'url' => 'https://example.com/test-news',
            'source' => 'Test Unit'
        ];

        $this->client->request(
            'POST',
            '/api/feeds',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $this->assertEquals(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());

        $createdData = json_decode($this->client->getResponse()->getContent(), true);
        $feedId = $createdData['id'];

        // 2. Delete the created Feed
        $this->client->request('DELETE', '/api/feeds/' . $feedId);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
    }
}
