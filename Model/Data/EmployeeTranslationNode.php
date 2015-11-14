<?php

/**
 * This file is part of the DinecatEmployeeBundle package.
 * @copyright   2015 UAB Dinecat, http://dinecat.com/
 * @license     http://dinecat.com/licenses/mit MIT License
 * @link        https://github.com/dinecat/EmployeeBundle
 */

namespace Dinecat\EmployeeBundle\Model\Data;

use Dinecat\DataStructures\Collection\Collection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EmployeeTranslation node of Employee data object.
 * @package     DinecatEmployeeBundle
 * @subpackage  Model.Data
 * @author      Mykola Zyk <relo.san.pub@gmail.com>
 */
class EmployeeTranslationNode
{
    /**
     * @var string
     * @Assert\Language
     */
    public $lang;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Length(min="3", max="100")
     */
    public $firstname;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Length(min="3", max="100")
     */
    public $lastname;

    /**
     * @var string|null
     * @Assert\Length(min="2", max="100")
     */
    public $slug;

    /**
     * @var string|null
     * @Assert\Length(min="3", max="1000")
     */
    public $brief;

    /**
     * @var string|null
     * @Assert\Length(min="3", max="20000")
     */
    public $description;

    /**
     * @var Collection
     * @Assert\NotBlank
     * @Assert\Type(type="object")
     */
    public $options;

    /**
     * Constructor.
     * @param   string  $lang   Language identifier in ISO 639-1 standard.
     */
    public function __construct($lang)
    {
        $this->options = new Collection;
        $this->lang = $lang;
    }
}
