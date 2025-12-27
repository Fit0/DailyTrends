<?php

namespace App\Controller;

use App\Entity\Feed;
use App\DTO\FeedInputDTO;
use App\Service\FeedService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;

#[Route('/api/feeds', name: 'api_feeds_')]
#[OA\Tag(name: 'Feeds')]
final class FeedController extends AbstractController
{
    public function __construct(private FeedService $feedService)
    {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    #[OA\Get(
        summary: 'List all news feeds',
        description: 'Retrieves a collection of all news items stored in the database, ordered by creation date.',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success: Returns an array of feeds.',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: Feed::class, groups: ['feed:read']))
                )
            )
        ]
    )]
    public function index(): JsonResponse
    {
        return $this->json($this->feedService->getAllFeeds(), context: ['groups' => 'feed:read']);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        summary: 'Get a specific feed',
        description: 'Retrieves the details of a single feed by its unique ID.',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', description: 'The feed ID', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success: Returns the feed details.',
                content: new Model(type: Feed::class, groups: ['feed:read'])
            ),
            new OA\Response(response: 404, description: 'Feed not found')
        ]
    )]
    public function show(int $id): JsonResponse
    {
        return $this->json($this->feedService->getFeed($id), context: ['groups' => 'feed:read']);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Create a new feed entry',
        description: 'Manually adds a new news item to the system.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: new Model(type: FeedInputDTO::class))
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Feed created successfully',
                content: new Model(type: Feed::class, groups: ['feed:read'])
            ),
            new OA\Response(response: 400, description: 'Invalid payload or validation failed')
        ]
    )]
    public function create(#[MapRequestPayload] FeedInputDTO $dto): JsonResponse
    {
        $feed = $this->feedService->createFeed($dto);
        return $this->json($feed, Response::HTTP_CREATED, context: ['groups' => 'feed:read']);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    #[OA\Put(
        summary: 'Update an existing feed',
        description: 'Updates all fields of a specific feed entry.',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', description: 'The feed ID to update', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: new Model(type: FeedInputDTO::class))
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Feed updated successfully',
                content: new Model(type: Feed::class, groups: ['feed:read'])
            ),
            new OA\Response(response: 404, description: 'Feed not found'),
            new OA\Response(response: 400, description: 'Invalid input')
        ]
    )]
    public function update(int $id, #[MapRequestPayload] FeedInputDTO $dto): JsonResponse
    {
        $feed = $this->feedService->updateFeed($id, $dto);
        return $this->json($feed, context: ['groups' => 'feed:read']);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        summary: 'Delete a feed entry',
        description: 'Removes a specific feed from the database.',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', description: 'The feed ID to delete', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 204, description: 'Feed deleted successfully'),
            new OA\Response(response: 404, description: 'Feed not found')
        ]
    )]
    public function delete(int $id): JsonResponse
    {
        $this->feedService->deleteFeed($id);
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

}
