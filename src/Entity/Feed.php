<?php

namespace App\Entity;

use App\Repository\FeedRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FeedRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Feed
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['feed:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['feed:read'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['feed:read'])]
    private ?string $body = null;

    #[ORM\Column(length: 500)]
    #[Groups(['feed:read'])]
    private ?string $url = null;

    #[ORM\Column(length: 50)]
    #[Groups(['feed:read'])]
    private ?string $source = null;

    #[ORM\Column]
    #[Groups(['feed:read'])]
    private ?\DateTimeImmutable $created_at = null;

    public function __construct()
    {
        if ($this->created_at === null) {
            $this->created_at = new \DateTimeImmutable();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): static
    {
        $this->source = $source;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }
}
