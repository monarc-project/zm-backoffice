<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2022  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Monarc\Core\Entity\Traits\CreateEntityTrait;
use Monarc\Core\Entity\Traits\UpdateEntityTrait;

/**
 * @ORM\Table(name="servers")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Server
{
    use CreateEntityTrait;
    use UpdateEntityTrait;

    public const STATUS_ACTIVE = 1;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var ArrayCollection|Client[]
     *
     * @ORM\OneToMany(targetEntity="Client", mappedBy="server")
     */
    protected $clients;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255)
     */
    protected $label;

    /**
     * @var string
     *
     * @ORM\Column(name="ip_address", type="string", length=64)
     */
    protected $ipAddress = '';

    /**
     * @var string
     *
     * @ORM\Column(name="fqdn", type="string", length=255)
     */
    protected $fqdn = '';

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean", nullable=true)
     */
    protected $status = self::STATUS_ACTIVE;

    public function __construct(array $data)
    {
        $this->setLabel($data['label'])
            ->setIpAddress($data['ipAddress'])
            ->setFqdn($data['fqdn']);

        $this->clients = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getClients()
    {
        return $this->clients;
    }

    public function addClient(Client $client): self
    {
        if (!$this->clients->contains($client)) {
            $this->clients->add($client);
            $client->setServer($this);
        }

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    public function getFqdn(): string
    {
        return $this->fqdn;
    }

    public function setFqdn(string $fqdn): self
    {
        $this->fqdn = $fqdn;

        return $this;
    }

    public function isActive(): bool
    {
        return (bool)$this->status;
    }

    public function setStatus(bool $status)
    {
        $this->status = (int)$status;

        return $this;
    }
}
