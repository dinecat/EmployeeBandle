<?php

/**
 * This file is part of the DinecatEmployeeBundle package.
 * @copyright   2015 UAB Dinecat, http://dinecat.com/
 * @license     http://dinecat.com/licenses/mit MIT License
 * @link        https://github.com/dinecat/EmployeeBundle
 */

namespace Dinecat\EmployeeBundle\Model\Repository\Doctrine\Bridge;

use Dinecat\DataStructures\Entity\Doctrine\Bridge;
use Dinecat\EmployeeBundle\Model\Data;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * Log entity bridge.
 * @package     DinecatEmployeeBundle
 * @subpackage  Model.Repository.Doctrine.Bridge
 * @author      Mykola Zyk <relo.san.pub@gmail.com>
 *
 * @ORM\Entity
 * @ORM\Table(
 *     name="din_employee_log",
 *     indexes={@ORM\Index(name="din_employee_log_object_idx", columns={"object_id", "entity_id"})}
 * )
 */
class LogBridge extends Bridge
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\SequenceGenerator(sequenceName="din_employee_log_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;

    /**
     * @var integer
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
     * @var integer
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
     * @var string
     * @ORM\Column(name="params", type="json_extra", nullable=true)
     */
    protected $params = [];

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime;
    }

    /**
     * Get identifier of the log record.
     * @return  integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Import data from dataset.
     * @param   Data\LogData    $dataset
     * @param   EntityManager   $em
     * @return  static
     * @throws  \Dinecat\DataStructures\Exception\IdentifiersNotMatch   If entity and dataset identifier's not matched.
     * @throws  \Dinecat\DataStructures\Exception\IncompleteDataset     If imported dataset marked as partial/empty.
     */
    public function import(Data\LogData $dataset, EntityManager $em)
    {
        $this->matchIds($this->id, $dataset->id);
        $this->validateDataset($dataset);

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
     * @return  Data\LogData
     */
    public function export()
    {
        $dataset = new Data\LogData;
        $dataset->id = $this->id;
        $dataset->objectId = $this->objectId;
        $dataset->entityId = $this->entity->getId();
        $dataset->actionId = $this->action->getId();
        $dataset->versionId = $this->versionId;
        $dataset->employeeId = $this->employee->getId();
        $dataset->params->replaceAll($this->params);
        $dataset->createdAt = $this->createdAt;
        $dataset->setDatasetCompletion(true);
        return $dataset;
    }
}
