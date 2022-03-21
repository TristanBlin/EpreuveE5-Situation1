<?php

namespace App\Entity;
use App\Entity\Produit;

use App\Repository\FormationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FormationRepository::class)
 */
class Formation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $date_debut;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbres_heures;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $departement;
        /**
     * @ORM\ManyToOne(targetEntity="Produit")
     * @ORM\JoinColumn()
     */
    private $produit;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): self
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getNbresHeures(): ?int
    {
        return $this->nbres_heures;
    }

    public function setNbresHeures(int $nbres_heures): self
    {
        $this->nbres_heures = $nbres_heures;

        return $this;
    }

    public function getDepartement(): ?string
    {
        return $this->departement;
    }

    public function setDepartement(string $departement): self
    {
        $this->departement = $departement;

        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): self
    {
        $this->produit = $produit;

        return $this;
    }
}
