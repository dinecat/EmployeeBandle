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
 * Data object for log record of employee action.
 * @package DinecatEmployeeBundle\Model\Data
 * @author  Mykola Zyk <relo.san.pub@gmail.com>
 */
class LogData extends Dataset
{
    /**
     * @var int|null
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @var int
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $objectId;

    /**
     * @var int
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $entityId;

    /**
     * @var int
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $actionId;

    /**
     * @var int|null
     * @Assert\Type(type="integer")
     */
    public $versionId;

    /**
     * @var int
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $employeeId;

    /**
     * @var Collection
     * @Assert\NotBlank
     * @Assert\Type(type="object")
     */
    public $params;

    /**
     * @var \DateTime|null
     * @Assert\DateTime
     */
    public $createdAt;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->params = new Collection();
    }
}
