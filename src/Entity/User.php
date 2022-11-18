<?php
declare(strict_types=1);
namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Enum\RoleEnum;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(),
        new Post(),
        new GetCollection()
    ],
    normalizationContext: [
        'groups' => ['read']
    ]
)
]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['username', 'email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 32, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 32)]
    #[Groups(['read'])]
    private string $username;

    #[ORM\Column(type: Types::STRING, length: 32, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private string $email;

    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

    #[ORM\Column]
    #[Assert\NotBlank]
    private ?string $password;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Expression(
        "this.getPassword() == getRepeatPassword()",
        message: "The password is not the same."
    )]
    private string $repeatPassword;

    #[ORM\Column]
    private ?string $plainPassword;

    #[ORM\Column(nullable: true)]
    private ?string $logo;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $enabled;

    #[ORM\OneToMany('commentBy', Comment::class)]
    #[Groups(['read'])]
    private Collection $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        if (!in_array(RoleEnum::ROLE_USER->name, $roles, true)) {
            $roles[] = RoleEnum::ROLE_USER->name;
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getRepeatPassword(): string
    {
        return $this->repeatPassword;
    }

    public function setRepeatPassword(string $repeatPassword): self
    {
        $this->repeatPassword = $repeatPassword;
        return $this;
    }


    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;
        return $this;
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;
        return $this;
    }

    public function getComments(): Collection
    {
        return new ArrayCollection($this->comments->toArray());
    }

    public function containsComments(Comment $comment): bool
    {
        return $this->comments->contains($comment);
    }

    public function addComment(Comment $comment): void
    {
        if (!$this->containsComments($comment)) {
            $this->comments->add($comment);
            $comment->setCommentBy($this);
        }
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment) && $comment->getCommentBy() === $this) {
            $comment->setCommentBy(null);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->email;
    }
}
