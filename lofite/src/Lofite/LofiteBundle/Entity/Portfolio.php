<?php

namespace Lofite\LofiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Portfolio
 *
 * @ORM\Table(name="portfolio")
 * @ORM\Entity
 */
class Portfolio
{

    /**
     * @ORM\OneToMany(targetEntity="Images", mappedBy="portfolio",cascade={"persist", "remove"})
     */
    private $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }


    public function setImages($images)
    {
        if (count($images) > 0) {
            foreach ($images as $i) {
                $this->addImage($i);
            }
        }

        return $this;
    }

    public function addImage($image)
    {
        $image->setPortfolio($this);

        $this->images->add($image);
    }

    public function removeImage($image)
    {
        $this->images->removeElement($image);
        $image->setPortfolio(null);
    }

    public function getImages()
    {
        return $this->images;
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="technologies", type="string", length=255, nullable=true)
     */
    private $technologies;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=255, nullable=true)
     */
    private $link;



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Portfolio
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
     * @return Portfolio
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
     * Set technologies
     *
     * @param string $technologies
     * @return Portfolio
     */
    public function setTechnologies($technologies)
    {
        $this->technologies = $technologies;

        return $this;
    }

    /**
     * Get technologies
     *
     * @return string 
     */
    public function getTechnologies()
    {
        return $this->technologies;
    }

    /**
     * Set link
     *
     * @param string $link
     * @return Portfolio
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string 
     */
    public function getLink()
    {
        return $this->link;
    }


    public function __toString()
    {
        return $this->getName();
    }
}
