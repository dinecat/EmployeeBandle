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
use Dinecat\EmployeeBundle\Model\Data\AuthLogData;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entity bridge for auth log record.
 * @package DinecatEmployeeBundle\Model\Repository\Doctrine
 * @author  Mykola Zyk <relo.san.pub@gmail.com>
 *
 * @ORM\Entity
 * @ORM\Table(
 *     name="din_employee_auth_log",
 *     indexes={
 *         @ORM\Index(name="din_employee_auth_log_employee_idx", columns={"employee_id"})
 *     }
 * )
 */
class AuthLogBridge
{
    /**
     * @var int|null
     * @ORM\Id
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\SequenceGenerator(sequenceName="din_employee_auth_log_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;

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
     * @var string
     * @ORM\Column(name="ip", type="string", length=39, nullable=false)
     */
    protected $ip;

    /**
     * @var string
     * @ORM\Column(name="params", type="json_extra", nullable=true)
     */
    protected $params = [];

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    protected $createdAt;

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
     * @param   AuthLogData     $dataset
     * @param   EntityManager   $em
     * @return  static
     * @throws  IdentifiersNotMatchException    If entity and dataset identifier's not matched.
     * @throws  IncompleteDatasetException      If imported dataset marked as partial/empty.
     */
    public function import(AuthLogData $dataset, EntityManager $em)
    {
        if ($this->id && $this->id !== $dataset->id) {
            throw new IdentifiersNotMatchException(get_class($this), $this->id, $dataset->id);
        }

        if (!$dataset->isDatasetComplete()) {
            throw new IncompleteDatasetException(get_class($this), $this->id);
        }

        $this->employee = $em->getReference(
            'Dinecat\EmployeeBundle\Model\Repository\Doctrine\Bridge\EmployeeBridge',
            $dataset->employeeId
        );

        $this->ip = $dataset->ip;
        $this->params = $dataset->params->toArray();

        return $this;
    }

    /**
     * Export data to dataset.
     * @return  AuthLogData
     */
    public function export()
    {
        $dataset = new AuthLogData;
        $dataset->id = $this->id;
        $dataset->employeeId = $this->employee->getId();
        $dataset->ip = $this->ip;
        $dataset->params->replaceAll($this->params);
        $dataset->createdAt = clone $this->createdAt;
        $dataset->setDatasetCompletion(true);
        return $dataset;
    }
}
