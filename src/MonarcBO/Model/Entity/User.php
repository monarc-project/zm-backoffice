<?php

namespace MonarcBO\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use MonarcCore\Model\Entity\AbstractEntity;

/**
 * Class User
 *
 * @package MonarcBO\Model\Entity
 * @ORM\Table(name="users")
 * @ORM\Entity
 */
class User extends AbstractEntity {
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="current_anr_id", type="integer")
     */
    protected $current_anr_id;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=255)
     */
    protected $role;

    /**
     * @var string
     *
     * @ORM\Column(name="date_start", type="date")
     */
    protected $date_start;

    /**
     * @var string
     *
     * @ORM\Column(name="date_end", type="date")
     */
    protected $date_end;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="smallint")
     */
    protected $status;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255)
     */
    protected $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255)
     */
    protected $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=20)
     */
    protected $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=255)
     */
    protected $salt;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    protected $password;

    /**
     * @var string
     *
     * @ORM\Column(name="creator", type="string", length=255)
     */
    protected $creator;

    /**
     * @var string
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $created_at;

    /**
     * @var string
     *
     * @ORM\Column(name="updater", type="string", length=255)
     */
    protected $updater;

    /**
     * @var string
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updated_at;
}