<?php

namespace App\Service;

use App\Entity\Feed;
use App\DTO\FeedInputDTO;
use App\Repository\FeedRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FeedService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
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

    public function createFeed(FeedInputDTO $dto): Feed
    {
        $feed = new Feed();
        $this->mapDtoToEntity($dto, $feed);

        $this->entityManager->persist($feed);
        $this->entityManager->flush();
        return $feed;
    }

    public function updateFeed(int $id, FeedInputDTO $dto): Feed
    {
        $feed = $this->getFeed($id);
        $this->mapDtoToEntity($dto, $feed);

        $this->entityManager->flush();
        return $feed;
    }

    public function deleteFeed(int $id): void
    {
        $feed = $this->getFeed($id);
        $this->entityManager->remove($feed);
        $this->entityManager->flush();
    }

    private function mapDtoToEntity(FeedInputDTO $dto, Feed $feed): void
    {
        $feed->setTitle($dto->title);
        $feed->setBody($dto->body);
        $feed->setUrl($dto->url);
        $feed->setSource($dto->source);
    }
}
