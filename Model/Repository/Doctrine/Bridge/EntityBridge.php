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
use Doctrine\ORM\Mapping as ORM;

/**
 * Entity entity bridge.
 * @package     DinecatEmployeeBundle
 * @subpackage  Model.Repository.Doctrine.Bridge
 * @author      Mykola Zyk <relo.san.pub@gmail.com>
 *
 * @ORM\Entity
 * @ORM\Table(
 *     name="din_employee_entity",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="din_employee_entity_name_idx", columns={"name"})
 *     }
 * )
 */
class EntityBridge extends Bridge
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\SequenceGenerator(sequenceName="din_employee_entity_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=70, nullable=false)
     */
    protected $name;

    /**
     * @var boolean
     * @ORM\Column(name="is_enabled", type="boolean", nullable=false)
     */
    protected $isEnabled;

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
        $this->createdAt = new \DateTime;
        $this->updatedAt = new \DateTime;
    }

    /**
     * Get identifier of the entity.
     * @return  integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Chack if entity enabled.
     * @return  boolean TRUE if Entity enabled, FALSE otherwise.
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * Get name of the entity.
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Import data from dataset.
     * @param   Data\EntityData $dataset
     * @return  static
     */
    public function import(Data\EntityData $dataset)
    {
        $this->matchIds($this->id, $dataset->id);
        $this->validateDataset($dataset);

        $this->name = $dataset->name;
        $this->isEnabled = $dataset->isEnabled;
        $this->rules = $dataset->rules->toArray();

        $this->translations = array_map(
            function ($item) {
                /** @var Data\EntityTranslationNode $item */
                return [
                    'title' => $item->title,
                    'slug' => $item->slug,
                    'description' => $item->description
                ];
            },
            $dataset->translations->toArray()
        );

        $this->updatedAt = new \DateTime;
        return $this;
    }

    /**
     * Export data to dataset.
     * @return  Data\EntityData
     */
    public function export()
    {
        $dataset = new Data\EntityData;
        $dataset->id = $this->id;
        $dataset->name = $this->name;
        $dataset->isEnabled = $this->isEnabled;
        $dataset->rules->replaceAll($this->rules);

        foreach ($this->translations as $lang => $set) {
            $node = new Data\EntityTranslationNode($lang);
            $node->title = $set['title'];
            $node->slug = $set['slug'];
            $node->description = $set['description'];
            $dataset->translations->set($lang, $node);
        }

        $dataset->createdAt = $this->createdAt;
        $dataset->updatedAt = $this->updatedAt;
        $dataset->setCompletion(true);
        return $dataset;
    }
}
