<?php

namespace App\Controller;

use App\Service\FeedService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

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
}
