<?php

namespace TicketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\user;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ticket
 *
 * @ORM\Table(name="ticket")
 * @ORM\Entity(repositoryClass="TicketBundle\Repository\ticketRepository")
 */
class ticket {

    public function __construct() {
        $this->dateDeadline = new \DateTime();

    }

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var bool $done
     *
     * @ORM\Column(name="done", type="boolean")
     */
    private $done = false;


    /**
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\user")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $userCreated;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation", type="datetime", nullable=true)
     */
    private $dateCreation;

    /**
     * @var \DateTime
     *
     * @Assert\NotBlank(message="insert deadline")
     * @ORM\Column(name="date_deadline", type="datetime")
     */
    private $dateDeadline;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="insert ticket name")
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var int
     *
     * @Assert\Type(
     *      type="integer",
     *      message="points value given ({{value}}) is not a valid {{type}} type."
     * )
     * @Assert\NotBlank(message="insert number of sprint points")
     * @ORM\Column(name="number_points", type="integer")
     */
    private $numberPoints;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="insert project in which ticket should belong")
     * @ORM\Column(name="project", type="string", length=255)
     */
    private $project;

    /**
     * @ORM\Column(name="slug", unique=true)
     * @Gedmo\Slug(fields={"id", "name",}, updatable=false)
     */
    private $slug;



    /* ----------------------------------------------------- */



    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set userCreated
     *
     * @param user $userCreated
     *
     * @return ticket
     */
    public function setUserCreated($userCreated)
    {
        $this->userCreated = $userCreated;

        return $this;
    }

    /**
     * Get userCreated
     *
     * @return user
     */
    public function getUserCreated()
    {
        return $this->userCreated;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return ticket
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set dateDeadline
     *
     * @param \DateTime $dateDeadline
     *
     * @return ticket
     */
    public function setDateDeadline($dateDeadline)
    {
        $this->dateDeadline = $dateDeadline;

        return $this;
    }

    /**
     * Get dateDeadline
     *
     * @return \DateTime
     */
    public function getDateDeadline()
    {
        return $this->dateDeadline;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return ticket
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return ticket
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set numberPoints
     *
     * @param integer $numberPoints
     *
     * @return ticket
     */
    public function setNumberPoints($numberPoints)
    {
        $this->numberPoints = $numberPoints;

        return $this;
    }

    /**
     * Get numberPoints
     *
     * @return int
     */
    public function getNumberPoints()
    {
        return $this->numberPoints;
    }

    /**
     * Set project
     *
     * @param string $project
     *
     * @return ticket
     */
    public function setProject($project)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return string
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @return string
     */
    public function isDoneString()
    {
        if($this->done == true)
            return "task was completed.";
        else
            return "task in progress";
    }

    public function isDoneBoolean()
    {
        return $this->done;
    }

    /**
     * @param boolean $done
     */
    public function setDone($done)
    {
        $this->done = $done;
    }



    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }
}

