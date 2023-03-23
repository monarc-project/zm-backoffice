<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2021 SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Monarc\BackOffice\Validator\UniqueClientProxyAlias;
use Monarc\Core\Model\Entity\AbstractEntity;
use Monarc\Core\Model\Entity\Traits\CreateEntityTrait;
use Monarc\Core\Model\Entity\Traits\UpdateEntityTrait;

/**
 * Clients
 *
 * @ORM\Table(name="clients")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Client extends AbstractEntity
{
    use CreateEntityTrait;
    use UpdateEntityTrait;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var ArrayCollection|ClientModel[]
     *
     * @ORM\OneToMany(targetEntity="ClientModel", mappedBy="client", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $models;

    /**
     * @var integer
     *
     * @ORM\Column(name="server_id", type="integer", nullable=true)
     */
    protected $server_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="logo_id", type="integer", nullable=true)
     */
    protected $logo_id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="proxy_alias", type="string", length=255, nullable=true)
     */
    protected $proxyAlias;

    /**
     * @var string
     *
     * @ORM\Column(name="contact_email", type="string", length=255, nullable=true)
     */
    protected $contact_email;

    /**
     * @var string
     *
     * @ORM\Column(name="first_user_firstname", type="string", length=255, nullable=true)
     */
    protected $first_user_firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="first_user_lastname", type="string", length=255, nullable=true)
     */
    protected $first_user_lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="first_user_email", type="string", length=255, nullable=true)
     */
    protected $first_user_email;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_two_factor_auth_enforced", type="boolean", nullable=false, options={"default":true})
     */
    protected $twoFactorAuthEnforced = true;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_background_import_active", type="boolean", nullable=false, options={"default":false})
     */
    protected $isBackgroundImportActive = false;

    public function __construct($obj = null)
    {
        $this->models = new ArrayCollection();

        parent::__construct($obj);
    }

    public function getModels()
    {
        return $this->models;
    }

    public function isTwoFactorAuthEnforced(): bool
    {
        return $this->twoFactorAuthEnforced;
    }

    public function setTwoFactorAuthEnforced(bool $twoFactorAuthEnforced): self
    {
        $this->twoFactorAuthEnforced = $twoFactorAuthEnforced;

        return $this;
    }

    public function isBackgroundImportActive(): bool
    {
        return $this->isBackgroundImportActive;
    }

    public function setIsBackgroundImportActive(bool $isBackgroundImportActive): self
    {
        $this->isBackgroundImportActive = $isBackgroundImportActive;

        return $this;
    }

    public function getInputFilter($partial = false)
    {
        if (!$this->inputFilter) {
            parent::getInputFilter($partial);

            $this->inputFilter->add(
                array(
                    'name' => 'name',
                    'required' => ($partial) ? false : true,
                    'filters' => array(
                        array('name' => 'StringTrim',),
                    ),
                    'validators' => array(),
                )
            );

            $validators = array();
            if (!$partial) {
                $validators[] = array(
                    'name' => UniqueClientProxyAlias::class,
                    'options' => array(
                        'adapter' => $this->getDbAdapter(),
                        'id' => $this->get('id'),
                    ),
                );
            }
            $this->inputFilter->add(
                array(
                    'name' => 'proxyAlias',
                    'required' => ($partial) ? false : true,
                    'filters' => array(
                        array('name' => 'StringTrim',),
                    ),
                    'validators' => $validators
                )
            );
        }
        return $this->inputFilter;
    }
}
