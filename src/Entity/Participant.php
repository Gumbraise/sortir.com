<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Serializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\HasLifecycleCallbacks]
#[ORM\EntityListeners]
#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Il y a déjà un compte avec cet email')]
#[UniqueEntity(fields: ['pseudo'], message: 'Il y a déjà un compte avec ce pseudo')]
class Participant implements UserInterface, PasswordAuthenticatedUserInterface, Serializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180,unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $telephone = null;

    #[ORM\Column]
    private ?bool $actif = null;

    #[ORM\ManyToOne(inversedBy: 'participants')]
    private ?Campus $campus = null;

    #[ORM\OneToMany(mappedBy: 'organisateur', targetEntity: Sortie::class)]
    private Collection $sortiesOrganisees;

    #[ORM\ManyToMany(targetEntity: Sortie::class, mappedBy: 'participants')]
    private Collection $sorties;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Ignore]
    private $profilePictureName;

    #[Vich\UploadableField(mapping: 'profile_picture', fileNameProperty: 'profilePictureName')]
    #[Assert\File(maxSize: '8192k', mimeTypes: ["image/jpeg", "image/png", "image/gif"])]
    #[Ignore]
    private $profilePictureFile;

    #[ORM\Column]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $pseudo = null;

    public bool $passwordChanged = false;

    public function __toString()
    {
        return $this->pseudo;
    }

    public function __construct()
    {
        $this->actif = true;
        $this->updatedAt = new DateTimeImmutable();

        $this->sortiesOrganisees = new ArrayCollection();
        $this->sorties = new ArrayCollection();
    }
    public function serialize(): string
    {
        return serialize([
            $this->id,
            $this->email,
            $this->roles,
            $this->password,
            $this->nom,
            $this->prenom,
            $this->telephone,
            $this->actif,
            $this->campus,
            $this->sortiesOrganisees,
            $this->sorties,
            $this->updatedAt,
            $this->pseudo,
            // Exclude the $profilePictureName and $profilePictureFile properties
        ]);
    }

    public function unserialize($serialized): void
    {
        [
            $this->id,
            $this->email,
            $this->roles,
            $this->password,
            $this->nom,
            $this->prenom,
            $this->telephone,
            $this->actif,
            $this->campus,
            $this->sortiesOrganisees,
            $this->sorties,
            $this->updatedAt,
            $this->pseudo,
        ] = unserialize($serialized);
    }

    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'roles' => $this->roles,
            'password' => $this->password,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'telephone' => $this->telephone,
            'actif' => $this->actif,
            'campus' => $this->campus,
            'sortiesOrganisees' => $this->sortiesOrganisees,
            'sorties' => $this->sorties,
            'updatedAt' => $this->updatedAt,
            'pseudo' => $this->pseudo,
        ];
    }

    public function __unserialize(array $serialized)
    {
        $this->id = $serialized['id'];
        $this->email = $serialized['email'];
        $this->roles = $serialized['roles'];
        $this->password = $serialized['password'];
        $this->nom = $serialized['nom'];
        $this->prenom = $serialized['prenom'];
        $this->telephone = $serialized['telephone'];
        $this->actif = $serialized['actif'];
        $this->campus = $serialized['campus'];
        $this->sortiesOrganisees = $serialized['sortiesOrganisees'];
        $this->sorties = $serialized['sorties'];
        $this->updatedAt = $serialized['updatedAt'];
        $this->pseudo = $serialized['pseudo'];
        return $this;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }


    public function setPassword(string $password): static
    {
        $this->password = $password;
        $this->passwordChanged = true;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): static
    {
        $this->actif = $actif;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): static
    {
        $this->campus = $campus;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSortiesOrganisees(): Collection
    {
        return $this->sortiesOrganisees;
    }

    public function addSortiesOrganisee(Sortie $sortiesOrganisee): static
    {
        if (!$this->sortiesOrganisees->contains($sortiesOrganisee)) {
            $this->sortiesOrganisees->add($sortiesOrganisee);
            $sortiesOrganisee->setOrganisateur($this);
        }

        return $this;
    }

    public function removeSortiesOrganisee(Sortie $sortiesOrganisee): static
    {
        if ($this->sortiesOrganisees->removeElement($sortiesOrganisee)) {
            // set the owning side to null (unless already changed)
            if ($sortiesOrganisee->getOrganisateur() === $this) {
                $sortiesOrganisee->setOrganisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSorties(): Collection
    {
        return $this->sorties;
    }

    public function addSortie(Sortie $sortie): static
    {
        if (!$this->sorties->contains($sortie)) {
            $this->sorties->add($sortie);
            $sortie->addParticipant($this);
        }

        return $this;
    }

    public function removeSortie(Sortie $sortie): static
    {
        if ($this->sorties->removeElement($sortie)) {
            $sortie->removeParticipant($this);
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getProfilePictureName(): ?string
    {
        return $this->profilePictureName ?? 'default-user.jpg';
    }

    /**
     * @param string|null $profilePictureName
     * @return $this
     */
    public function setProfilePictureName(?string $profilePictureName): static
    {
        $this->profilePictureName = $profilePictureName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProfilePictureFile()
    {
        return $this->profilePictureFile;
    }

    /**
     * @param $profilePictureFile
     * @return $this
     */
    public function setProfilePictureFile(File $profilePictureFile = null): static
    {
        $this->profilePictureFile = $profilePictureFile;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($profilePictureFile) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new DateTimeImmutable('now');
        }

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getFullname(): ?string
    {
        return $this->prenom . ' ' . $this->nom;
    }
}
