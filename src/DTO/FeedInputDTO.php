<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

/**
 * Data Transfer Object for Feed input operations.
 */
class FeedInputDTO
{
    public function __construct(
        #[OA\Property(
            description: 'The headline or title of the news article',
            maxLength: 255,
            example: 'New advancements in renewable energy'
        )]
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public string $title,

        #[OA\Property(
            description: 'The full content or a summary of the news body',
            example: 'Researchers have discovered a more efficient way to store solar energy...'
        )]
        #[Assert\NotBlank]
        public string $body,

        #[OA\Property(
            description: 'The original source URL of the article',
            maxLength: 500,
            example: 'https://www.nature.com/articles/example-energy-2025'
        )]
        #[Assert\NotBlank]
        #[Assert\Url(requireTld: true)]
        #[Assert\Length(max: 500)]
        public string $url,

        #[OA\Property(
            description: 'The name of the media outlet or publisher',
            maxLength: 50,
            example: 'Nature Magazine'
        )]
        #[Assert\NotBlank]
        #[Assert\Length(max: 50)]
        public string $source,
    ) {}
}
