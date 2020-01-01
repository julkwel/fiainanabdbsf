<?php
/**
 * Julien Rajerison <julienrajerison5@gmail.com>
 **/

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Fiainana.
 *
 * @ORM\Entity()
 */
class Fiainana
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $dateAdd;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $publicationDate;

    /**
     * @var string
     *
     * @ORM\Column(type="string",nullable=true)
     */
    private $avatar;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean",options={"default":0})
     */
    private $isPublie;

    /**
     * Fiainana constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->dateAdd = new DateTime('now');
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Fiainana
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Fiainana
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Fiainana
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getDateAdd(): ?DateTime
    {
        return $this->dateAdd;
    }

    /**
     * @param DateTime $dateAdd
     *
     * @return Fiainana
     */
    public function setDateAdd(DateTime $dateAdd): self
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getPublicationDate(): ?DateTime
    {
        return $this->publicationDate;
    }

    /**
     * @param DateTime $publicationDate
     *
     * @return Fiainana
     */
    public function setPublicationDate(DateTime $publicationDate): self
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    /**
     * @param string $avatar
     *
     * @return Fiainana
     */
    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPublie(): bool
    {
        return $this->isPublie;
    }

    /**
     * @param bool $isPublie
     *
     * @return Fiainana
     */
    public function setIsPublie(?bool $isPublie): self
    {
        $this->isPublie = $isPublie;

        return $this;
    }
}
