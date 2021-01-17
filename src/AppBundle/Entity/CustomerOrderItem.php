<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CustomerOrderItem
 *
 * @ORM\Table(name="customer_order_item")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CustomerOrderItemRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class CustomerOrderItem
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
     * @var int
     *
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @Assert\NotBlank
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CustomerOrder", inversedBy="customerOrderItems")
     * @ORM\JoinColumn(nullable=false)
     */
    private $customerOrder;

    /**
     * @Assert\NotBlank
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Item", inversedBy="customerOrderItems")
     * @ORM\JoinColumn(nullable=false)
     */
    private $item;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set amount.
     *
     * @param int $amount
     *
     * @return CustomerOrderItem
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount.
     *
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return CustomerOrderItem
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
     * Get created.
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return CustomerOrder
     */
    public function getCustomerOrder()
    {
        return $this->customerOrder;
    }

    /**
     * @param CustomerOrder $customerOrder
     *
     * @return CustomerOrderItem
     */
    public function setCustomerOrder(CustomerOrder $customerOrder)
    {
        $this->customerOrder = $customerOrder;

        return $this;

    }

    /**
     * @return Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param Item $item
     *
     * @return CustomerOrderItem
     */
    public function setItem(Item $item)
    {
        $this->item = $item;

        return $this;

    }
}
