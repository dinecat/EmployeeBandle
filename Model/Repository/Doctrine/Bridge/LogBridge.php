<?php

/**
 * This file is part of the DinecatEmployeeBundle package.
 * @copyright   2015 UAB Dinecat, http://dinecat.com/
 * @license     http://dinecat.com/licenses/mit MIT License
 * @link        https://github.com/dinecat/EmployeeBundle
 */

namespace Dinecat\EmployeeBundle\Model\Repository\Doctrine\Bridge;

use Dinecat\DataStructures\Exception\IdentifiersNotMatchException;
use Dinecat\DataStructures\Exception\IncompleteDatasetException;
use Dinecat\EmployeeBundle\Model\Data\LogData;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entity bridge for log record.
 * @package DinecatEmployeeBundle\Model\Repository\Doctrine
 * @author  Mykola Zyk <relo.san.pub@gmail.com>
 *
 * @ORM\Entity
 * @ORM\Table(
 *     name="din_employee_log",
 *     indexes={@ORM\Index(name="din_employee_log_object_idx", columns={"object_id", "entity_id"})}
 * )
 */
class LogBridge
{
    /**
     * @var int|null
     * @ORM\Id
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\SequenceGenerator(sequenceName="din_employee_log_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;

    /**
     * @var int
     * @ORM\Column(name="object_id", type="bigint", nullable=false)
     */
    protected $objectId;

    /**
     * @var EntityBridge
     * @ORM\ManyToOne(
     *     targetEntity="Dinecat\EmployeeBundle\Model\Repository\Doctrine\Bridge\EntityBridge",
     *     fetch="EXTRA_LAZY",
     *     cascade={"persist"}
     * )
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id", nullable=false, onDelete="restrict")
     */
    protected $entity;

    /**
     * @var ActionBridge
     * @ORM\ManyToOne(
     *     targetEntity="Dinecat\EmployeeBundle\Model\Repository\Doctrine\Bridge\ActionBridge",
     *     fetch="EXTRA_LAZY",
     *     cascade={"persist"}
     * )
     * @ORM\JoinColumn(name="action_id", referencedColumnName="id", nullable=false, onDelete="restrict")
     */
    protected $action;

    /**
     * @var int|null
     * @ORM\Column(name="version_id", type="bigint", nullable=true)
     */
    protected $versionId;

    /**
     * @var EmployeeBridge
     * @ORM\ManyToOne(
     *     targetEntity="Dinecat\EmployeeBundle\Model\Repository\Doctrine\Bridge\EmployeeBridge",
     *     fetch="EXTRA_LAZY",
     *     cascade={"persist"}
     * )
     * @ORM\JoinColumn(name="employee_id", referencedColumnName="id", nullable=false, onDelete="restrict")
     */
    protected $employee;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @var array
     * @ORM\Column(name="params", type="json_extra", nullable=true)
     */
    protected $params = [];

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * Get identifier of the log record.
     * @return  int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Import data from dataset.
     * @param   LogData         $dataset
     * @param   EntityManager   $em
     * @return  static
     * @throws  IdentifiersNotMatchException    If entity and dataset identifier's not matched.
     * @throws  IncompleteDatasetException      If imported dataset marked as partial/empty.
     */
    public function import(LogData $dataset, EntityManager $em)
    {
        if ($this->id && $this->id !== $dataset->id) {
            throw new IdentifiersNotMatchException(get_class($this), $this->id, $dataset->id);
        }

        if (!$dataset->isDatasetComplete()) {
            throw new IncompleteDatasetException(get_class($this), $this->id);
        }

        $this->entity = $em->getReference(
            'Dinecat\EmployeeBundle\Model\Repository\Doctrine\Bridge\EntityBridge',
            $dataset->entityId
        );

        $this->action = $em->getReference(
            'Dinecat\EmployeeBundle\Model\Repository\Doctrine\Bridge\ActionBridge',
            $dataset->actionId
        );

        $this->employee = $em->getReference(
            'Dinecat\EmployeeBundle\Model\Repository\Doctrine\Bridge\EmployeeBridge',
            $dataset->employeeId
        );

        $this->objectId = $dataset->objectId;
        $this->versionId = $dataset->versionId;
        $this->params = $dataset->params->toArray();

        return $this;
    }

    /**
     * Export data to dataset.
     * @return  LogData
     */
    public function export()
    {
        $dataset = new LogData;
        $dataset->id = $this->id;
        $dataset->objectId = $this->objectId;
        $dataset->entityId = $this->entity->getId();
        $dataset->actionId = $this->action->getId();
        $dataset->versionId = $this->versionId;
        $dataset->employeeId = $this->employee->getId();
        $dataset->params->replaceAll($this->params);
        $dataset->createdAt = clone $this->createdAt;
        $dataset->setDatasetCompletion(true);
        return $dataset;
    }
}
