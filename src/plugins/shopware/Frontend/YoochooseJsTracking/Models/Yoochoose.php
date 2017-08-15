<?php

namespace Shopware\Models\Yoochoose;

use Shopware\Components\Model\ModelEntity,
    Shopware\Models\Shop\Shop as Shop,
    Doctrine\ORM\Mapping as ORM,
    Symfony\Component\Validator\Constraints as Assert;

/**
 * Shopware Yoochoose Model
 *
 * @ORM\Entity()
 * @ORM\Table(name="s_yoochoose_config")
 */
class Yoochoose extends ModelEntity
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $name
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private $name;

    /**
     * @var string $value
     *
     * @ORM\Column(name="value", type="string", nullable=true)
     */
    private $value;

    /**
     *
     * @ORM\ManyToOne(targetEntity="\Shopware\Models\Shop\Shop")
     * @ORM\JoinColumn(name="shop_id", referencedColumnName="id", onDelete="CASCADE")
     * @var Shop
     */
    private $shop;

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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set id
     *
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Set value
     *
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Get value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set shop id
     *
     * @param Shop $shop
     */
    public function setShop(Shop $shop)
    {
        $this->shop = $shop;
    }

    /**
     * Get shop id
     *
     * @return Shop
     */
    public function getShop()
    {
        return $this->shop;
    }
}
