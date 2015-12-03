<?php

/**
 * This file is part of the DinecatEmployeeBundle package.
 * @copyright   2015 UAB Dinecat, http://dinecat.com/
 * @license     http://dinecat.com/licenses/mit MIT License
 * @link        https://github.com/dinecat/EmployeeBundle
 */

namespace Dinecat\EmployeeBundle\Model\Data;

use Dinecat\DataStructures\Collection\Collection;
use Dinecat\DataStructures\Entity\Dataset;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data object for entity type.
 * @package DinecatEmployeeBundle\Model\Data
 * @author  Mykola Zyk <relo.san.pub@gmail.com>
 */
class EntityData extends Dataset
{
    /**
     * @var int|null
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Length(min="3", max="70")
     */
    public $name;

    /**
     * @var bool
     * @Assert\NotBlank
     * @Assert\Type(type="bool")
     */
    public $enabled;

    /**
     * @var Collection
     * @Assert\NotBlank
     * @Assert\Type(type="object")
     */
    public $rules;

    /**
     * @var Collection|EntityTranslationNode[]
     * @Assert\NotBlank
     * @Assert\Count(min="1", max="100")
     * @Assert\Type(type="object")
     * @Assert\Valid
     */
    public $translations;

    /**
     * @var \DateTime|null
     * @Assert\DateTime
     */
    public $createdAt;

    /**
     * @var \DateTime|null
     * @Assert\DateTime
     */
    public $updatedAt;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->rules = new Collection();
        $this->translations = new Collection();
    }
}
