<?php

/**
 * This file is part of the DinecatEmployeeBundle package.
 * @copyright   2015 UAB Dinecat, http://dinecat.com/
 * @license     http://dinecat.com/licenses/mit MIT License
 * @link        https://github.com/dinecat/EmployeeBundle
 */

namespace Dinecat\EmployeeBundle\Model\Data;

use Dinecat\DataStructures\Entity\NodeInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ActionTranslation node of Action data object.
 * @package     DinecatEmployeeBundle
 * @subpackage  Model.Data
 * @author      Mykola Zyk <relo.san.pub@gmail.com>
 */
class ActionTranslationNode implements NodeInterface
{
    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Language
     */
    public $lang;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Length(min="2", max="200")
     */
    public $title;

    /**
     * @var string|null
     * @Assert\Length(min="2", max="100")
     */
    public $slug;

    /**
     * @var string|null
     * @Assert\Length(min="10", max="20000")
     */
    public $description;

    /**
     * Constructor.
     * @param   string  $lang   Language identifier in ISO 639-1 standard.
     */
    public function __construct($lang)
    {
        $this->lang = $lang;
    }
}
