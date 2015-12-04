<?php

/**
 * This file is part of the DinecatEmployeeBundle package.
 * @copyright   2015 UAB Dinecat, http://dinecat.com/
 * @license     http://dinecat.com/licenses/mit MIT License
 * @link        https://github.com/dinecat/EmployeeBundle
 */

namespace Dinecat\EmployeeBundle\Model\Repository\Doctrine\Bridge;

use Dinecat\EmployeeBundle\Model\Data\EmployeeTranslationNode;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entity bridge for employee translation.
 * @package DinecatEmployeeBundle\Model\Repository\Doctrine
 * @author  Mykola Zyk <relo.san.pub@gmail.com>
 *
 * @ORM\Entity
 * @ORM\Table(name="din_employee_t9n")
 */
class EmployeeTranslationBridge
{
    /**
     * @var EmployeeBridge
     * @ORM\Id
     * @ORM\ManyToOne(
     *     targetEntity="Dinecat\EmployeeBundle\Model\Repository\Doctrine\Bridge\EmployeeBridge",
     *     inversedBy="translations",
     *     fetch="EXTRA_LAZY",
     *     cascade={"persist"}
     * )
     * @ORM\JoinColumn(name="id", referencedColumnName="id", nullable=false, onDelete="cascade")
     */
    protected $employee;

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="lang", type="string", length=2, nullable=false)
     */
    protected $lang;

    /**
     * @var string
     * @ORM\Column(name="firstname", type="string", length=100, nullable=false)
     */
    protected $firstname;

    /**
     * @var string
     * @ORM\Column(name="lastname", type="string", length=100, nullable=false)
     */
    protected $lastname;

    /**
     * @var string|null
     * @ORM\Column(name="brief", type="text", length=1000, nullable=true)
     */
    protected $brief;

    /**
     * @var string|null
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var string
     * @ORM\Column(name="options", type="json_extra", nullable=true)
     */
    protected $options = [];

    /**
     * Constructor.
     * @param   EmployeeBridge  $employee
     * @param   string          $lang       Language identifier in ISO 639-1 standard.
     */
    public function __construct(EmployeeBridge $employee, $lang)
    {
        $this->employee = $employee;
        $this->lang = $lang;
    }

    /**
     * Get language identifier in ISO 639-1 standard.
     * @return  string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * Import data from dataset node.
     * @param   EmployeeTranslationNode $node
     * @return  static
     */
    public function import(EmployeeTranslationNode $node)
    {
        $this->firstname = $node->firstname;
        $this->lastname = $node->lastname;
        $this->brief = $node->brief;
        $this->description = $node->description;
        $this->options = $node->options->toArray();
        return $this;
    }

    /**
     * Export data to dataset node.
     * @return  EmployeeTranslationNode
     */
    public function export()
    {
        $node = new EmployeeTranslationNode($this->lang);
        $node->firstname = $this->firstname;
        $node->lastname = $this->lastname;
        $node->brief = $this->brief;
        $node->description = $this->description;
        $node->options->replaceAll($this->options);
        return $node;
    }
}
