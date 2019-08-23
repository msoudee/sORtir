<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SortieRepository")
 *
 */
class Sortie
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;



    /**
     * @ORM\Column(type="string", length=30)
     */
    private $nom;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\GreaterThanOrEqual("now",
     *     message="La date de début doit être supérieur ou égal à la date du jour")
     * )
     */
    private $dateDebut;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duree;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Expression("this.getDateDebut() > this.getDateCloture() and this.getDateActuelle() < this.getDateCloture()",
     *     message="La date de cloture doit être inférieur à la date de début")
     * )
     */
    private $dateCloture;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbInscriptionsMax;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Etat")
     * @ORM\JoinColumn(nullable=false)
     */
    private $etat;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Lieu")
     * @ORM\JoinColumn(nullable=false)
     */
    private $lieu;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Site")
     * @ORM\JoinColumn(nullable=false)
     */
    private $site;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organisateur;
    private $nbInscriptions;
    private $inscrit;
    private $actions;
    private $cbOrganisateur, $cbInscrit, $cbNonInscrit, $cbTerminees;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateCloture(): ?\DateTimeInterface
    {
        return $this->dateCloture;
    }

    public function setDateCloture(\DateTimeInterface $dateCloture): self
    {
        $this->dateCloture = $dateCloture;

        return $this;
    }

    public function getNbInscriptionsMax(): ?int
    {
        return $this->nbInscriptionsMax;
    }

    public function setNbInscriptionsMax(int $nbInscriptionsMax): self
    {
        $this->nbInscriptionsMax = $nbInscriptionsMax;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    public function setEtat(?Etat $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): self
    {
        $this->site = $site;

        return $this;
    }

    public function getOrganisateur(): ?User
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?User $organisateur): self
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    public function getNbInscriptions()
    {
        return $this->nbInscriptions;
    }

    public function setNbInscriptions($nbInscriptions): void
    {
        $this->nbInscriptions = $nbInscriptions;
    }

    public function getInscrit(): bool
    {
        return $this->inscrit;
    }

    public function setInscrit(bool $inscrit)
    {
        $this->inscrit = $inscrit;
    }

    /**
     * @return mixed
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param mixed $actions
     */
    public function setActions($actions)
    {
        $this->actions = $actions;
    }

    /**
     * @return mixed
     */
    public function getCbOrganisateur()
    {
        return $this->cbOrganisateur;
    }

    /**
     * @param mixed $cbOrganisateur
     */
    public function setCbOrganisateur($cbOrganisateur): void
    {
        $this->cbOrganisateur = $cbOrganisateur;
    }

    /**
     * @return mixed
     */
    public function getCbInscrit()
    {
        return $this->cbInscrit;
    }

    /**
     * @param mixed $cbInscrit
     */
    public function setCbInscrit($cbInscrit): void
    {
        $this->cbInscrit = $cbInscrit;
    }

    /**
     * @return mixed
     */
    public function getCbNonInscrit()
    {
        return $this->cbNonInscrit;
    }

    /**
     * @param mixed $cbNonInscrit
     */
    public function setCbNonInscrit($cbNonInscrit): void
    {
        $this->cbNonInscrit = $cbNonInscrit;
    }

    /**
     * @return mixed
     */
    public function getCbTerminees()
    {
        return $this->cbTerminees;
    }

    /**
     * @param mixed $cbTerminees
     */
    public function setCbTerminees($cbTerminees): void
    {
        $this->cbTerminees = $cbTerminees;
    }

    

    public function getDateActuelle(){
        return new \DateTime();
    }


}
