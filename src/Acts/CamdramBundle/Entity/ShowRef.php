<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ShowsRef
 *
 * @ORM\Table(name="acts_shows_refs")
 * @ORM\Entity
 */
class ShowRef
{
    /**
     * @var integer
     *
     * @ORM\Column(name="refid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Show
     *
     * @ORM\ManyToOne(targetEntity="Show")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="showid", referencedColumnName="id")
     * })
     */
    private $show_id;

    /**
     * @var string
     *
     * @ORM\Column(name="ref", type="text", nullable=false)
     */
    private $ref;


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
     * Set ref
     *
     * @param string $ref
     * @return ShowRef
     */
    public function setRef($ref)
    {
        $this->ref = $ref;
    
        return $this;
    }

    /**
     * Get ref
     *
     * @return string 
     */
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * Set show_id
     *
     * @param \Acts\CamdramBundle\Entity\Show $showId
     * @return ShowRef
     */
    public function setShowId(\Acts\CamdramBundle\Entity\Show $showId = null)
    {
        $this->show_id = $showId;
    
        return $this;
    }

    /**
     * Get show_id
     *
     * @return \Acts\CamdramBundle\Entity\Show 
     */
    public function getShowId()
    {
        return $this->show_id;
    }
}