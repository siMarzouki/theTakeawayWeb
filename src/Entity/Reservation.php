<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=ReservationRepository::class)
 */
class Reservation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank
     * @Assert\GreaterThan("today")
     * ('la date est incorrecte')
     */
    private $date;

    /**
     * @ORM\Column(type="time")
     * @Assert\Time
     * @var string A "H:i" formatted value
     */
    private $heureArrive;

    /**
     * @ORM\Column(type="time")
     * @Assert\Time
     * @var string A "H:i" formatted value
     */
    private $heureDepart;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(
     *      min = 1,
     *      max = 10,
     *      notInRangeMessage = "Nombre de personns entre 1 et 10",
     * )
     */
    private $nbPersonne;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\Choice({"En Attente", "Annulé", "Réfusé","Accepté"})
     */
    private $statut;

    /**
     * @ORM\ManyToMany(targetEntity=Table::class)
     */
    private $tables;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class)
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull
     */
    private $clientId;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class)
     */
    private $adminCharge;


    /**
     * @ORM\ManyToOne(targetEntity=Restaurant::class)
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull
     */
    private $restaurant;

    public function __construct()
    {
        $this->tables = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getHeureArrive(): ?\DateTimeInterface
    {
        return $this->heureArrive;
    }

    public function setHeureArrive(\DateTimeInterface $heureArrive): self
    {
        $this->heureArrive = $heureArrive;

        return $this;
    }

    public function getHeureDepart(): ?\DateTimeInterface
    {
        return $this->heureDepart;
    }

    public function setHeureDepart(\DateTimeInterface $heureDepart): self
    {
        $this->heureDepart = $heureDepart;

        return $this;
    }

    public function getNbPersonne(): ?int
    {
        return $this->nbPersonne;
    }

    public function setNbPersonne(int $nbPersonne): self
    {
        $this->nbPersonne = $nbPersonne;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }



    public function getClientId(): ?Client
    {
        return $this->clientId;
    }

    public function setClientId(?Client $clientId): self
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function getAdminCharge(): ?Client
    {
        return $this->adminCharge;
    }

    public function setAdminCharge(?Client $adminCharge): self
    {
        $this->adminCharge = $adminCharge;

        return $this;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): self
    {
        $this->commande = $commande;

        return $this;
    }

    public function getRestaurant(): ?Restaurant
    {
        return $this->restaurant;
    }

    public function setRestaurant(?Restaurant $restaurant): self
    {
        $this->restaurant = $restaurant;

        return $this;
    }

    /**
     * @return Collection|Table[]
     */
    public function getTables(): Collection
    {
        return $this->tables;
    }

    public function addTable(Table $table): self
    {
        if (!$this->tables->contains($table)) {
            $this->tables[] = $table;
        }

        return $this;
    }

    public function removeTable(Table $table): self
    {
        $this->tables->removeElement($table);

        return $this;
    }
}
