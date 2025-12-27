<?php

namespace App\Service;

use App\Entity\Feed;
use App\Repository\FeedRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FeedService
{
    public function __construct(
        private FeedRepository $feedRepository)
        {}

    public function getAllFeeds(): array
    {
        return $this->feedRepository->findBy([], ['created_at' => 'DESC']);
    }

    public function getFeed(int $id): Feed
    {
        $feed = $this->feedRepository->find($id);

        if(!$feed) {
            throw new NotFoundHttpException('Feed not found');
        }

        return $feed;
    }

}
