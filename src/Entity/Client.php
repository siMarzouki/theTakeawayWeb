<?php

namespace App\Entity;
use App\Entity\RestaurantFavoris;
use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;






/**
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 *
 *
 */
class Client implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     *@Assert\NotBlank
     */
    private $email;




    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", length=240, nullable=true)
     */
    private $password;
 

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     *@Assert\Type("String")
     */
    private $nom;
 

    /**
     *
     * @ORM\Column(type="string", length=100 , nullable=true)
     * @Assert\Type("String")
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Type("String")
     */
    private $num_tel;
    
 
    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type("integer")
     *
     */
    private $points;



    /**
     * @ORM\Column(type="datetime" , nullable=true)
     */
    private $Date_curent;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $facebookID;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $facebookAccessToken;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $githubID;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $githubAccessToken;

    /**
     * @ORM\Column(type="boolean", length=255, nullable=true)
     */
    private $IsVerified;

    /**
     * @ORM\OneToMany(targetEntity=CartBancaire::class, mappedBy="client", orphanRemoval=true)
     */
    private $cards;


    /**
     * @ORM\OneToMany(targetEntity=RestaurantFavoris::class, mappedBy="client")
     */
    private $favoris;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $brochureFilename;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $status;
    public function __construct()
    {
        $this->restaurant = new ArrayCollection();
        $this->cards = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->nom;
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

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    


    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): self
    {
        $this->points = $points;

        return $this;
    }



    public function getDateCurent(): ?\DateTimeInterface
    {
        return $this->Date_curent;
    }

    public function setDateCurent(\DateTimeInterface $Date_curent): self
    {
        $this->Date_curent = $Date_curent;

        return $this;
    }
    public function getNumTel(): ?String
    {
        return $this->num_tel;
    }

    public function setNumTel(String $num_tel): self
    {
        $this->num_tel = $num_tel;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFacebookAccessToken()
    {
        return $this->facebookAccessToken;
    }

    /**
     * @param mixed $facebookAccessToken
     */
    public function setFacebookAccessToken($facebookAccessToken): void
    {
        $this->facebookAccessToken = $facebookAccessToken;
    }

    /**
     * @return mixed
     */
    public function getFacebookID()
    {
        return $this->facebookID;
    }

    /**
     * @param mixed $facebookID
     */
    public function setFacebookID($facebookID): void
    {
        $this->facebookID = $facebookID;
    }

    /**
     * @return mixed
     */
    public function getGithubID()
    {
        return $this->githubID;
    }

    /**
     * @param mixed $githubID
     */
    public function setGithubID($githubID): void
    {
        $this->githubID = $githubID;
    }

    /**
     * @return mixed
     */
    public function getGithubAccessToken()
    {
        return $this->githubAccessToken;
    }

    /**
     * @param mixed $githubAccessToken
     */
    public function setGithubAccessToken($githubAccessToken): void
    {
        $this->githubAccessToken = $githubAccessToken;
    }

    /**
     * @return mixed
     */
    public function getIsVerified()
    {
        return $this->IsVerified;
    }

    /**
     * @param mixed $IsVerified
     */
    public function setIsVerified($IsVerified): void
    {
        $this->IsVerified = $IsVerified;
    }

    /**
     * @return Collection<int, CartBancaire>
     */
    public function getCards(): Collection
    {
        return $this->cards;
    }

    public function addCard(CartBancaire $card): self
    {
        if (!$this->cards->contains($card)) {
            $this->cards[] = $card;
            $card->setClient($this);
        }

        return $this;
    }

    public function removeCard(CartBancaire $card): self
    {
        if ($this->cards->removeElement($card)) {
            // set the owning side to null (unless already changed)
            if ($card->getClient() === $this) {
                $card->setClient(null);
            }
        }

        return $this;
    }
    /**
     * @return Collection<int, RestaurantFavoris>
     */
    public function getFavoris(): Collection
    {
        return $this->favoris;
    }

    public function addFavori(RestaurantFavoris $favori): self
    {
        if (!$this->favoris->contains($favori)) {
            $this->favoris[] = $favori;
            $favori->setClient($this);
        }

        return $this;
    }

    public function removeFavori(RestaurantFavoris $favori): self
    {
        if ($this->favoris->removeElement($favori)) {
            // set the owning side to null (unless already changed)
            if ($favori->getClient() === $this) {
                $favori->setClient(null);
            }
        }

        return $this;
    }

    public function getBrochureFilename(): ?string
    {
        return $this->brochureFilename;
    }

    public function setBrochureFilename(?string $brochureFilename): self
    {
        $this->brochureFilename = $brochureFilename;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): self
    {
        $this->status = $status;

        return $this;
    }



}
