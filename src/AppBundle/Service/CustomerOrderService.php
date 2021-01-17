<?php

namespace AppBundle\Service;


use AppBundle\Entity\CustomerOrder;
use AppBundle\Entity\CustomerOrderItem;
use AppBundle\Entity\Item;
use Doctrine\ORM\EntityManager;

class CustomerOrderService
{
    const STATUS_ORDER_ARCHIVED = -1;
    const STATUS_ORDER_IN_PROGRESS = 0;
    const STATUS_ORDER_PAID = 1;
    const STATUS_ORDER_DELIVERED = 2;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * CustomerService constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param null $customerId
     *
     * @return CustomerOrder[]
     */
    public function getCustomerOrders($customerId = null)
    {
        $orderRepo = $this->em->getRepository(CustomerOrder::class);

        if (is_null($customerId)) {
            $customerOrders = $orderRepo->findAll();
        } else {
            $customerOrders = $orderRepo->findBy(['customer' => $customerId]);
        }

        return $customerOrders;
    }

    /**
     * @param CustomerOrder $order
     * @param int           $orderStatus
     *
     * @return CustomerOrder
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createCustomerOrder(CustomerOrder $order, $orderStatus = CustomerOrderService::STATUS_ORDER_IN_PROGRESS)
    {
        $order->setStatus($orderStatus);
        $this->em->persist($order);
        $this->em->flush();

        return $order;
    }

    /**
     * @param CustomerOrder $order
     * @param Item          $item
     * @param int           $amount
     *
     * @return CustomerOrder
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createCustomerOrderItem(CustomerOrder $order, Item $item, $amount)
    {
        $customerOrderItem = new CustomerOrderItem();
        $customerOrderItem->setCustomerOrder($order);
        $customerOrderItem->setItem($item);
        $customerOrderItem->setAmount($amount);
        $this->em->persist($customerOrderItem);
        $this->em->flush();

        return $order;
    }

    /**
     * @param CustomerOrder $order
     *
     * @return CustomerOrder
     * @throws \Doctrine\ORM\ORMException
     */
    public function updateCustomerOrder(CustomerOrder $order)
    {
        $this->em->merge($order);
        $this->em->flush();

        return $order;
    }

    /**
     * @param CustomerOrder $order
     *
     * @return CustomerOrder
     * @throws \Doctrine\ORM\ORMException
     */
    public function deleteCustomerOrder(CustomerOrder $order)
    {
        $this->em->remove($order);
        $this->em->flush();

        return $order;
    }

    /**
     * @param CustomerOrder $order
     * @param Item          $item
     *
     * @return CustomerOrder
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteCustomerOrderItem(CustomerOrder $order, Item $item)
    {
        $customerOrderItem = $this->em
            ->getRepository(CustomerOrderItem::class)
            ->findOneBy(['customerOrder' => $order, 'item' => $item]);

        if ($customerOrderItem instanceof CustomerOrderItem) {
            $this->em->remove($customerOrderItem);
            $this->em->flush();
        }

        return $order;
    }
}