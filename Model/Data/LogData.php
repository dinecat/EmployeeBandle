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
 * LogData data object.
 * @package     DinecatEmployeeBundle
 * @subpackage  Model.Data
 * @author      Mykola Zyk <relo.san.pub@gmail.com>
 */
class LogData extends Dataset
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
    public $objectId;

    /**
     * @var integer
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $entityId;

    /**
     * @var integer
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $actionId;

    /**
     * @var integer
     * @Assert\Type(type="integer")
     */
    public $versionId;

    /**
     * @var integer
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
