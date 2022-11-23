<?php

declare(strict_types=1);

namespace App\Domain\Article\Entity;

use App\Application\Common\Entity\EntityInterface;
use App\Application\Common\Traits\IdTrait;
use App\Application\Common\Traits\SoftDeletableTrait;
use App\Application\Common\Traits\TimestampableTrait;
use App\Application\Common\Traits\UuidTrait;
use App\Domain\Article\Repository\ArticleRepository;
use App\Domain\Article\Validator as ArticleValidator;
use App\Domain\User\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ORM\Cache]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity('uuid')]
#[UniqueEntity('title')]
#[ArticleValidator\ArticlePublishedDateStatus]
class Article implements EntityInterface
{
    use IdTrait;
    use UuidTrait;
    use TimestampableTrait;
    use SoftDeletableTrait;

    #[Assert\Type(Types::STRING),
        Assert\NotBlank,
        Assert\Length(max: 128)]
    #[Groups(['article:read'])]
    #[ORM\Column(type: Types::STRING, length: 128, nullable: false)]
    private string $title;

    #[Assert\Type(Types::STRING),
        Assert\NotBlank]
    #[Groups(['article:read'])]
    #[ORM\Column(type: Types::TEXT)]
    private string $content;

    #[Assert\NotNull]
    #[Groups(['article:read'])]
    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private User $author;

    #[Assert\Type(Types::STRING),
        Assert\NotBlank,
        Assert\Length(max: 32)]
    #[Groups(['article:read'])]
    #[ORM\Column(type: Types::STRING, length: 32, nullable: false)]
    private string $status;

    #[Groups(['article:read'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $publishedAt;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeInterface $publishedAt): static
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }
}
