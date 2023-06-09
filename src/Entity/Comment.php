<?php
declare(strict_types=1);
namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(),
        new Put(
            security: "is_granted('IS_AUTHENTICATED_FULLY') and object.getUser() == user",
        ),
        new GetCollection(),
        new Post(
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
        ),
    ],
    denormalizationContext: [
        'groups' => ['post']
    ],
)
]
#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment implements OwnerInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'comment.blank')]
    #[Assert\Length(
        min: 5,
        max: 10000,
        minMessage: 'comment.too_short',
        maxMessage: 'comment.too_long'
    )]
    #[Groups(['post'])]
    private ?string $content;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Timestampable(on: 'create')]
    private \DateTime $publishedAt;

    public function __construct()
    {
        $this->publishedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?UserInterface $owner): OwnerInterface
    {
        $this->owner = $owner;
        return $this;
    }

    public function getPublishedAt(): \DateTime
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTime $publishedAt): self
    {
        $this->publishedAt = $publishedAt;
        return $this;
    }

    public function __toString(): string
    {
        return $this->content;
    }
}
