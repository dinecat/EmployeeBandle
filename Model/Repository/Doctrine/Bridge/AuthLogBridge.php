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
 * AuthLog entity bridge.
 * @package     DinecatEmployeeBundle
 * @subpackage  Model.Repository.Doctrine.Bridge
 * @author      Mykola Zyk <relo.san.pub@gmail.com>
 *
 * @ORM\Entity
 * @ORM\Table(
 *     name="din_employee_auth_log",
 *     indexes={
 *         @ORM\Index(name="din_employee_auth_log_employee_idx", columns={"employee_id"})
 *     }
 * )
 */
class AuthLogBridge extends Bridge
{
    /**
     * @var integer
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
     * @param   Data\AuthLogData    $dataset
     * @param   EntityManager       $em
     * @return  static
     */
    public function import(Data\AuthLogData $dataset, EntityManager $em)
    {
        $this->matchIds($this->id, $dataset->id);
        $this->validateDataset($dataset);

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
     * @return  Data\AuthLogData
     */
    public function export()
    {
        $dataset = new Data\AuthLogData;
        $dataset->id = $this->id;
        $dataset->employeeId = $this->employee->getId();
        $dataset->ip = $this->ip;
        $dataset->params->replaceAll($this->params);
        $dataset->createdAt = $this->createdAt;
        $dataset->setCompletion(true);
        return $dataset;
    }
}
