<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class FeedInputDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max:255)]
        public string $title,

        #[Assert\NotBlank]
        public string $body,

        #[Assert\NotBlank]
        #[Assert\Url(requireTld: true)]
        #[Assert\Length(max:500)]
        public string $url,

        #[Assert\NotBlank]
        #[Assert\Length(max:50)]
        public string $source,
    ) {}
}
