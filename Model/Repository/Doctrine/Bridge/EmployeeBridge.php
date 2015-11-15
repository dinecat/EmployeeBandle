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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * Employee entity bridge.
 * @package     DinecatEmployeeBundle
 * @subpackage  Model.Repository.Doctrine.Bridge
 * @author      Mykola Zyk <relo.san.pub@gmail.com>
 *
 * @ORM\Entity
 * @ORM\Table(
 *     name="din_employee",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="din_employee_username_idx", columns={"username_canonical"}),
 *         @ORM\UniqueConstraint(name="din_employee_email_idx", columns={"email_canonical"}),
 *     }
 * )
 */
class EmployeeBridge extends Bridge
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\SequenceGenerator(sequenceName="din_employee_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="username", type="string", length=150, nullable=false)
     */
    protected $username;

    /**
     * @var string
     * @ORM\Column(name="username_canonical", type="string", length=150, nullable=false)
     */
    protected $usernameCanonical;

    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=150, nullable=false)
     */
    protected $email;

    /**
     * @var string
     * @ORM\Column(name="email_canonical", type="string", length=150, nullable=false)
     */
    protected $emailCanonical;

    /**
     * @var boolean
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     */
    protected $enabled = false;

    /**
     * @var boolean
     * @ORM\Column(name="locked", type="boolean", nullable=false)
     */
    protected $locked = false;

    /**
     * @var string
     * @ORM\Column(name="salt", type="string", length=250, nullable=false)
     */
    protected $salt;

    /**
     * @var string
     * @ORM\Column(name="password", type="string", length=250, nullable=false)
     */
    protected $password;

    /**
     * @var PositionBridge
     * @ORM\ManyToOne(
     *     targetEntity="Dinecat\EmployeeBundle\Model\Repository\Doctrine\Bridge\PositionBridge",
     *     fetch="EXTRA_LAZY",
     *     cascade={"persist"}
     * )
     * @ORM\JoinColumn(name="position_id", referencedColumnName="id", nullable=false, onDelete="restrict")
     */
    protected $position;

    /**
     * @var string
     * @ORM\Column(name="options", type="json_extra", nullable=true)
     */
    protected $options = [];

    /**
     * @var array
     * @ORM\Column(name="roles", type="json_array", nullable=true)
     */
    protected $roles = [];

    /**
     * @var ArrayCollection|EmployeeTranslationBridge[]
     * @ORM\OneToMany(
     *     targetEntity="Dinecat\EmployeeBundle\Model\Repository\Doctrine\Bridge\EmployeeTranslationBridge",
     *     mappedBy="employee",
     *     indexBy="lang",
     *     cascade={"persist", "remove"},
     *     fetch="EXTRA_LAZY"
     * )
     */
    protected $translations;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    protected $updatedAt;

    /**
     * @var \DateTime|null
     * @ORM\Column(name="logged_at", type="datetime", nullable=true)
     */
    protected $loggedAt;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection;
        $this->createdAt = new \DateTime;
        $this->updatedAt = new \DateTime;
    }

    /**
     * Get identifier of the employee.
     * @return  integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Update last logged time to now.
     * @return  static
     */
    public function updateLoggedAt()
    {
        $this->loggedAt = new \DateTime;
        return $this;
    }

    /**
     * Import data from dataset.
     * @param   Data\EmployeeData   $dataset
     * @param   EntityManager       $em
     * @return  static
     * @throws  \Dinecat\DataStructures\Exception\IdentifiersNotMatch   If entity and dataset identifier's not matched.
     * @throws  \Dinecat\DataStructures\Exception\IncompleteDataset     If imported dataset marked as partial/empty.
     */
    public function import(Data\EmployeeData $dataset, EntityManager $em)
    {
        $this->matchIds($this->id, $dataset->id);
        $this->validateDataset($dataset);

        $this->position = $em->getReference(
            'Dinecat\EmployeeBundle\Model\Repository\Doctrine\Bridge\PositionBridge',
            $dataset->positionId
        );

        $this->username = $dataset->username;
        $this->usernameCanonical = $dataset->usernameCanonical;
        $this->email = $dataset->email;
        $this->emailCanonical = $dataset->emailCanonical;
        $this->enabled = $dataset->enabled;
        $this->locked = $dataset->locked;
        $this->salt = $dataset->salt;
        $this->password = $dataset->password;
        $this->options = $dataset->options->toArray();
        $this->roles = $dataset->roles->toArray();

        array_map(
            function ($lang) use ($dataset) {
                if (!$this->translations->containsKey($lang)) {
                    $this->translations->set(
                        $lang,
                        (new EmployeeTranslationBridge($this, $lang))->import($dataset->translations->get($lang))
                    );
                } elseif (!$dataset->translations->has($lang)) {
                    $this->translations->remove($lang);
                } else {
                    $this->translations->get($lang)->import($dataset->translations->get($lang));
                }
            },
            array_unique(array_merge($this->translations->getKeys(), $dataset->translations->getKeys()))
        );

        $this->updatedAt = new \DateTime;
        return $this;
    }

    /**
     * Export data to dataset.
     * @return  Data\EmployeeData
     */
    public function export()
    {
        $dataset = new Data\EmployeeData;
        $dataset->id = $this->id;
        $dataset->username = $this->username;
        $dataset->usernameCanonical = $this->usernameCanonical;
        $dataset->email = $this->email;
        $dataset->emailCanonical = $this->emailCanonical;
        $dataset->enabled = $this->enabled;
        $dataset->locked = $this->locked;
        $dataset->salt = $this->salt;
        $dataset->password = $this->password;
        $dataset->positionId = $this->position->getId();
        $dataset->options->replaceAll($this->options);
        $dataset->roles->replaceAll($this->roles);

        $dataset->translations->replaceAll(array_map(
            function ($translation) {
                /** @var EmployeeTranslationBridge $translation */
                return $translation->export();
            },
            $this->translations->toArray()
        ));

        $dataset->createdAt = $this->createdAt;
        $dataset->updatedAt = $this->updatedAt;
        $dataset->loggedAt = $this->loggedAt;
        $dataset->setDatasetCompletion(true);
        return $dataset;
    }
}
