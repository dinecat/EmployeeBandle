<?php

/**
 * This file is part of the DinecatEmployeeBundle package.
 * @copyright   2015 UAB Dinecat, http://dinecat.com/
 * @license     http://dinecat.com/licenses/mit MIT License
 * @link        https://github.com/dinecat/EmployeeBundle
 */

namespace Dinecat\EmployeeBundle\Model\Entity;

use Dinecat\EmployeeBundle\Model\Data;
use Dinecat\EmployeeBundle\Model\EmployeeDistributor;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * Employee entity.
 * @package     DinecatEmployeeBundle
 * @subpackage  Model.Entity
 * @author      Mykola Zyk <relo.san.pub@gmail.com>
 */
class Employee implements AdvancedUserInterface
{
    const CN_EMAIL = 'email';
    const CN_USERNAME = 'username';

    /**
     * @var Data\EmployeeData
     */
    private $data;

    /**
     * @var EmployeeDistributor
     */
    private $distributor;

    /**
     * @var string|null Preferred language identifier (ISO 639-1) [optional].
     */
    private $lang;

    /**
     * Constructor.
     * @param   Data\EmployeeData   $data
     * @param   EmployeeDistributor $distributor
     * @param   string|null         $lang           Preferred language identifier (ISO 639-1) [optional].
     */
    public function __construct(Data\EmployeeData $data, EmployeeDistributor $distributor, $lang = null)
    {
        $this->data = $data;
        $this->distributor = $distributor;
        $this->lang = $lang;
    }

    /**
     * Get identifier.
     * @return  integer
     */
    public function getId()
    {
        return $this->data->id;
    }

    /**
     * Get username.
     * @return  string
     */
    public function getUsername()
    {
        return $this->data->username;
    }

    /**
     * Get email.
     * @return  string
     */
    public function getEmail()
    {
        return $this->data->email;
    }

    /**
     * Check if employee enabled.
     * @return  boolean TRUE if employee enabled, FALSE otherwise.
     */
    public function isEnabled()
    {
        return $this->data->isEnabled;
    }

    /**
     * Get salt.
     * @return  string
     */
    public function getSalt()
    {
        return $this->data->salt;
    }

    /**
     * Get password hashe.
     * @return  string
     */
    public function getPassword()
    {
        return $this->data->password;
    }

    /**
     * Get first name.
     * @param   string  $lang   Language identifier in ISO 639-1 standard [optional].
     * @param   boolean $strict Return null instead of filename on existing language if requested language not exist [optional].
     * @return  string|null
     */
    public function getFirstname($lang = null, $strict = false)
    {
        $translation = $this->getTranslation($lang, $strict);
        return $translation ? $translation->firstname : null;
    }

    /**
     * Get last name.
     * @param   string  $lang   Language identifier in ISO 639-1 standard [optional].
     * @param   boolean $strict Return null instead of filename on existing language if requested language not exist [optional].
     * @return  string|null
     */
    public function getLastname($lang = null, $strict = false)
    {
        $translation = $this->getTranslation($lang, $strict);
        return $translation ? $translation->lastname : null;
    }

    /**
     * Check if employee option exist.
     * @param   string  $name   Option name.
     * @return  boolean TRUE if option exist, FALSE otherwise.
     */
    public function hasOption($name)
    {
        return $this->data->options->offsetExists($name);
    }

    /**
     * Get option value by name.
     * @param   string  $name   Option name.
     * @return  mixed
     */
    public function getOption($name)
    {
        return $this->data->options->has($name) ? $this->data->options->get($name) : null;
    }

    /**
     * Get employee roles.
     * @return array|\Symfony\Component\Security\Core\Role\Role[]
     */
    public function getRoles()
    {
        return $this->data->roles->toArray();
    }

    /**
     * Get date of employee creation.
     * @return  \DateTime
     */
    public function getCreatedAt()
    {
        return $this->data->createdAt;
    }

    /**
     * Get date of employee last modification.
     * @return  \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->data->updatedAt;
    }

    /**
     * Get date of employee last signin.
     * @return  \DateTime
     */
    public function getLoggedAt()
    {
        return $this->data->loggedAt;
    }

    /**
     * Set preferred language.
     * @param   string  $lang   Preferred language identifier (ISO 639-1).
     * @return  static
     */
    public function setPreferredLanguage($lang)
    {
        $this->lang = $lang;
        return $this;
    }

    /**
     * Get employee data object.
     * @return  Data\EmployeeData
     */
    public function getDataset()
    {
        return $this->data;
    }

    /**
     * {@inheritDoc}
     */
    public function eraseCredentials()
    {
        // not credentials = nothing to erasing.
    }

    /**
     * {@inheritDoc}
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isAccountNonLocked()
    {
        return !$this->data->isLocked;
    }

    /**
     * {@inheritDoc}
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * Get translation node.
     * @param   string|null $lang   Language identifier (ISO 639-1) [optional].
     * @param   boolean     $strict Return null instead of translation on existing language if requested language not exist [optional].
     * @return  Data\EmployeeTranslationNode|null
     */
    protected function getTranslation($lang = null, $strict = false)
    {
        if ($lang) {
            if ($this->data->translations->has($lang)) {
                return $this->data->translations->get($lang);
            } elseif ($strict) {
                return null;
            }
        }

        if ($this->lang) {
            if ($this->data->translations->has($this->lang)) {
                return $this->data->translations->get($this->lang);
            } elseif ($strict) {
                return null;
            }
        }

        return !$strict && $this->data->translations->count() ? $this->data->translations->current() : null;
    }
}
