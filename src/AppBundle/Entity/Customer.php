<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Customer
 *
 * @ORM\Table(name="customer")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CustomerRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Customer implements UserInterface, \Serializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="firstName", type="string", length=255)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="lastName", type="string", length=255)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=96, options={"comment":"Use password hash with BCRYPT"})
     */
    private $password;

    /**
     * A non-persisted field used to create the encoded password.
     *
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = "8",
     *      max = "4096",
     *      minMessage = "errors.customer.password_min_length",
     *      maxMessage = "errors.customer.password_max_length"
     * )
     */
    private $plainPassword;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="json", options={"comment":"Security roles"})
     */
    private $roles = [];

    public function __construct()
    {
        $this->roles = ['ROLE_CUSTOMER'];
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Customer
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Customer
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return Customer
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Customer
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
        $this->created = new \DateTime();

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Customer
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /** ### Methods to implement \Serializable, EquatableInterface ### */

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(
            [
                $this->id,
                $this->username,
                $this->password,
            ]
        );
    }

    /** @param $serialized
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            ) = unserialize($serialized, ['allowed_classes' => false]);
    }

    public function getSalt()
    {
        // Not needed as we are using BCRYPT, but we need to implement UserInterface.
        return null;
    }

    public function isEqualTo(UserInterface $member)
    {
        if ($this->username !== $member->getUsername()) {
            return false;
        }

        return true;
    }
    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param mixed $plainPassword
     *
     * @return Customer
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        // forces the object to look "dirty" to Doctrine. Avoids
        // Doctrine *not* saving this entity, if only plainPassword changes
        $this->password = null;

        return $this;
    }

    /* Methods to implements the interfaces to set this entity as a security provider:
     * -> https://symfony.com/doc/current/security/entity_provider.html
     */

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * @return mixed
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param mixed $roles
     *
     * @return Customer
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;

    }
    /** ### END Methods to implement \Serializable, EquatableInterface ### */
}

