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
use Dinecat\EmployeeBundle\Model\Data\ActionData;
use Dinecat\EmployeeBundle\Model\Data\ActionTranslationNode;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entity bridge for action type on entity object.
 * @package DinecatEmployeeBundle\Model\Repository\Doctrine
 * @author  Mykola Zyk <relo.san.pub@gmail.com>
 *
 * @ORM\Entity
 * @ORM\Table(
 *     name="din_employee_action",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="din_employee_action_name_idx", columns={"entity_id", "name"})
 *     }
 * )
 */
class ActionBridge
{
    /**
     * @var int|null
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\SequenceGenerator(sequenceName="din_employee_action_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;

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
     * @var string
     * @ORM\Column(name="name", type="string", length=70, nullable=false)
     */
    protected $name;

    /**
     * @var bool
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     */
    protected $enabled;

    /**
     * @var array
     * @ORM\Column(name="rules", type="json_extra", nullable=true)
     */
    protected $rules = [];

    /**
     * @var array
     * @ORM\Column(name="translations", type="json_extra", nullable=true)
     */
    protected $translations = [];

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
     * Constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * Get identifier of the action.
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Check if action enabled.
     * @return  bool    TRUE if action enabled, FALSE otherwise.
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Get name of the action.
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Import data from dataset.
     * @param   ActionData      $dataset
     * @param   EntityManager   $em
     * @return  static
     * @throws  IdentifiersNotMatchException    If entity and dataset identifier's not matched.
     * @throws  IncompleteDatasetException      If imported dataset marked as partial/empty.
     */
    public function import(ActionData $dataset, EntityManager $em)
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

        $this->name = $dataset->name;
        $this->enabled = $dataset->enabled;
        $this->rules = $dataset->rules->toArray();

        $this->translations = array_map(
            function ($item) {
                /** @var ActionTranslationNode $item */
                return [
                    'title' => $item->title,
                    'slug' => $item->slug,
                    'description' => $item->description
                ];
            },
            $dataset->translations->toArray()
        );

        $this->updatedAt = new \DateTime();
        return $this;
    }

    /**
     * Export data to dataset.
     * @return  ActionData
     */
    public function export()
    {
        $dataset = new ActionData;
        $dataset->id = $this->id;
        $dataset->entityId = $this->entity->getId();
        $dataset->name = $this->name;
        $dataset->enabled = $this->enabled;
        $dataset->rules->replaceAll($this->rules);

        foreach ($this->translations as $lang => $set) {
            $node = new ActionTranslationNode($lang);
            $node->title = $set['title'];
            $node->slug = $set['slug'];
            $node->description = $set['description'];
            $dataset->translations->set($lang, $node);
        }

        $dataset->createdAt = clone $this->createdAt;
        $dataset->updatedAt = clone $this->updatedAt;
        $dataset->setDatasetCompletion(true);
        return $dataset;
    }
}
