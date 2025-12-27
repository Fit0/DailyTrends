<?php

namespace App\Controller;

use App\DTO\FeedInputDTO;
use App\Service\FeedService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/feeds', name: 'api_feeds_')]
final class FeedController extends AbstractController
{
    public function __construct(private FeedService $feedService)
    {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json($this->feedService->getAllFeeds(), context: ['groups' => 'feed:read']);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        return $this->json($this->feedService->getFeed($id), context: ['groups' => 'feed:read']);
    }

    #[Route('', methods: ['POST'])]
    public function create(#[MapRequestPayload] FeedInputDTO $dto): JsonResponse
    {
        $feed = $this->feedService->createFeed($dto);
        return $this->json($feed, Response::HTTP_CREATED, context: ['groups' => 'feed:read']);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(int $id, #[MapRequestPayload] FeedInputDTO $dto): JsonResponse
    {
        $feed = $this->feedService->updateFeed($id, $dto);
        return $this->json($feed, context: ['groups' => 'feed:read']);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $this->feedService->deleteFeed($id);
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

}
