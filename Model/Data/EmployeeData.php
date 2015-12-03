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
 * Data object for employee.
 * @package DinecatEmployeeBundle\Model\Data
 * @author  Mykola Zyk <relo.san.pub@gmail.com>
 */
class EmployeeData extends Dataset
{
    /**
     * @var int|null
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Length(min="3", max="150")
     */
    public $username;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Length(min="3", max="150")
     */
    public $usernameCanonical;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Email
     * @Assert\Length(min="3", max="150")
     */
    public $email;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Length(min="3", max="150")
     */
    public $emailCanonical;

    /**
     * @var bool
     * @Assert\NotBlank
     * @Assert\Type(type="bool")
     */
    public $enabled;

    /**
     * @var bool
     * @Assert\NotBlank
     * @Assert\Type(type="bool")
     */
    public $locked;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Length(min="8", max="250")
     */
    public $salt;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Length(min="8", max="250")
     */
    public $password;

    /**
     * @var int
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $positionId;

    /**
     * @var Collection
     * @Assert\NotBlank
     * @Assert\Type(type="object")
     */
    public $options;

    /**
     * @var Collection
     * @Assert\NotBlank
     * @Assert\Count(min="1", max="100")
     * @Assert\Type(type="object")
     */
    public $roles;

    /**
     * @var Collection|EmployeeTranslationNode[]
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
     * @var \DateTime|null
     * @Assert\DateTime
     */
    public $loggedAt;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->options = new Collection();
        $this->roles = new Collection();
        $this->translations = new Collection();
    }
}
