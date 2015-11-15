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
 * Position entity bridge.
 * @package     DinecatEmployeeBundle
 * @subpackage  Model.Repository.Doctrine.Bridge
 * @author      Mykola Zyk <relo.san.pub@gmail.com>
 *
 * @ORM\Entity
 * @ORM\Table(
 *     name="din_employee_position",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="din_employee_position_name_idx", columns={"name"})
 *     }
 * )
 */
class PositionBridge extends Bridge
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\SequenceGenerator(sequenceName="din_employee_position_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=70, nullable=false)
     */
    protected $name;

    /**
     * @var boolean
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     */
    protected $enabled = false;

    /**
     * @var array
     * @ORM\Column(name="options", type="json_extra", nullable=true)
     */
    protected $options = [];

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
     * Get identifier of the position.
     * @return  integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Chack if position enabled.
     * @return  boolean TRUE if position enabled, FALSE otherwise.
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Get name of the position.
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Import data from dataset.
     * @param   Data\PositionData   $dataset
     * @return  static
     * @throws  \Dinecat\DataStructures\Exception\IdentifiersNotMatch   If entity and dataset identifier's not matched.
     * @throws  \Dinecat\DataStructures\Exception\IncompleteDataset     If imported dataset marked as partial/empty.
     */
    public function import(Data\PositionData $dataset)
    {
        $this->matchIds($this->id, $dataset->id);
        $this->validateDataset($dataset);

        $this->name = $dataset->name;
        $this->enabled = $dataset->enabled;
        $this->options = $dataset->options->toArray();

        $this->translations = array_map(
            function ($item) {
                /** @var Data\PositionTranslationNode $item */
                return [
                    'title' => $item->title,
                    'slug' => $item->slug,
                    'short' => $item->short,
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
     * @return  Data\PositionData
     */
    public function export()
    {
        $dataset = new Data\PositionData;
        $dataset->id = $this->id;
        $dataset->name = $this->name;
        $dataset->enabled = $this->enabled;
        $dataset->options->replaceAll($this->options);

        foreach ($this->translations as $lang => $set) {
            $node = new Data\PositionTranslationNode($lang);
            $node->title = $set['title'];
            $node->slug = $set['slug'];
            $node->short = $set['short'];
            $node->description = $set['description'];
            $dataset->translations->set($lang, $node);
        }

        $dataset->createdAt = $this->createdAt;
        $dataset->updatedAt = $this->updatedAt;
        $dataset->setDatasetCompletion(true);
        return $dataset;
    }
}
