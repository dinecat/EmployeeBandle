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
 * AuthLogData data object.
 * @package     DinecatEmployeeBundle
 * @subpackage  Model.Data
 * @author      Mykola Zyk <relo.san.pub@gmail.com>
 */
class AuthLogData extends Dataset
{
    /**
     * @var integer
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @var integer
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $employeeId;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Length(min="7", max="45")
     */
    public $ip;

    /**
     * @var Collection
     * @Assert\NotBlank
     * @Assert\Type(type="object")
     */
    public $params;

    /**
     * @var \DateTime
     * @Assert\DateTime
     */
    public $createdAt;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->params = new Collection;
    }
}
