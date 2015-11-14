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
 * EmployeeData data object.
 * @package     DinecatEmployeeBundle
 * @subpackage  Model.Data
 * @author      Mykola Zyk <relo.san.pub@gmail.com>
 */
class EmployeeData extends Dataset
{
    /**
     * @var integer
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
     * @var boolean
     * @Assert\NotBlank
     * @Assert\Type(type="bool")
     */
    public $isEnabled;

    /**
     * @var boolean
     * @Assert\NotBlank
     * @Assert\Type(type="bool")
     */
    public $isLocked;

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
     * @var integer
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
     * @var \DateTime
     * @Assert\NotBlank
     * @Assert\DateTime
     */
    public $createdAt;

    /**
     * @var \DateTime
     * @Assert\NotBlank
     * @Assert\DateTime
     */
    public $updatedAt;

    /**
     * @var \DateTime|null
     * @Assert\DateTime
     */
    public $lastLoggedAt;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->options = new Collection;
        $this->roles = new Collection;
        $this->translations = new Collection;
    }
}
