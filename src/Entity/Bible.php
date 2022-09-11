<?php
/**
 * @author <julienrajerison5@gmail.com>
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Bible.
 *
 * @ORM\Entity()
 */
class Bible
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string")
     */
    private $label;

    /**
     * @var integer|null
     *
     * @ORM\Column(type="integer")
     */
    private $chapter;

    /**
     * @var integer|null
     *
     * @ORM\Column(type="integer")
     */
    private $verse;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @param string|null $label
     *
     * @return Bible
     */
    public function setLabel(?string $label): Bible
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getChapter(): ?int
    {
        return $this->chapter;
    }

    /**
     * @param int|null $chapter
     *
     * @return Bible
     */
    public function setChapter(?int $chapter): Bible
    {
        $this->chapter = $chapter;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getVerse(): ?int
    {
        return $this->verse;
    }

    /**
     * @param int|null $verse
     *
     * @return Bible
     */
    public function setVerse(?int $verse): Bible
    {
        $this->verse = $verse;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string|null $content
     *
     * @return Bible
     */
    public function setContent(?string $content): Bible
    {
        $this->content = $content;

        return $this;
    }
}
