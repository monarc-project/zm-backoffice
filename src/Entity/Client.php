<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2022 SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Monarc\Core\Model\Entity\Traits\CreateEntityTrait;
use Monarc\Core\Model\Entity\Traits\UpdateEntityTrait;

/**
 * @ORM\Table(name="clients")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Client
{
    use CreateEntityTrait;
    use UpdateEntityTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var ArrayCollection|ClientModel[]
     *
     * @ORM\OneToMany(targetEntity="ClientModel", mappedBy="client")
     */
    protected $clientModels;

    /**
     * @var Server
     *
     * @ORM\ManyToOne(targetEntity="Server", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="server_id", referencedColumnName="id", nullable=false)
     * })
     */
    protected $server;

    /**
     * @var int
     *
     * @ORM\Column(name="logo_id", type="integer", nullable=false)
     */
    protected $logoId = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="proxy_alias", type="string", length=255, nullable=false)
     */
    protected $proxyAlias = '';

    /**
     * @var string
     *
     * @ORM\Column(name="contact_email", type="string", length=255, nullable=false)
     */
    protected $contactEmail = '';

    /**
     * @var string
     *
     * @ORM\Column(name="first_user_firstname", type="string", length=255, nullable=false)
     */
    protected $firstUserFirstname = '';

    /**
     * @var string
     *
     * @ORM\Column(name="first_user_lastname", type="string", length=255, nullable=false)
     */
    protected $firstUserLastname = '';

    /**
     * @var string
     *
     * @ORM\Column(name="first_user_email", type="string", length=255, nullable=false)
     */
    protected $firstUserEmail = '';

    public function __construct()
    {
        $this->clientModels = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getClientModels()
    {
        return $this->clientModels;
    }

    public function addClientModel(ClientModel $clientModel): self
    {
        if (!$this->clientModels->contains($clientModel)) {
            $this->clientModels->add($clientModel);
            $clientModel->setClient($this);
        }

        return $this;
    }

    public function removeClientModel(ClientModel $clientModel): self
    {
        if ($this->clientModels->contains($clientModel)) {
            $this->clientModels->remove($clientModel);
        }

        return $this;
    }

    public function getServer(): Server
    {
        return $this->server;
    }

    public function setServer(Server $server): self
    {
        $this->server = $server;
        $server->addClient($this);

        return $this;
    }

    public function getLogoId(): int
    {
        return (int)$this->logoId;
    }

    public function setLogoId(int $logoId): self
    {
        $this->logoId = $logoId;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getProxyAlias(): string
    {
        return $this->proxyAlias;
    }

    public function setProxyAlias(string $proxyAlias): self
    {
        $this->proxyAlias = $proxyAlias;

        return $this;
    }

    public function getContactEmail(): string
    {
        return (string)$this->contactEmail;
    }

    public function setContactEmail(string $contactEmail): self
    {
        $this->contactEmail = $contactEmail;

        return $this;
    }

    public function getFirstUserFirstname(): string
    {
        return $this->firstUserFirstname;
    }

    public function setFirstUserFirstname(string $firstUserFirstname): self
    {
        $this->firstUserFirstname = $firstUserFirstname;

        return $this;
    }

    public function getFirstUserLastname(): string
    {
        return $this->firstUserLastname;
    }

    public function setFirstUserLastname(string $firstUserLastname): self
    {
        $this->firstUserLastname = $firstUserLastname;

        return $this;
    }

    public function getFirstUserEmail(): string
    {
        return (string)$this->firstUserEmail;
    }

    public function setFirstUserEmail(string $firstUserEmail): self
    {
        $this->firstUserEmail = $firstUserEmail;

        return $this;
    }
}
