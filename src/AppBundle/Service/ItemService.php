<?php

namespace AppBundle\Service;


use AppBundle\Entity\Item;
use Doctrine\ORM\EntityManager;

class ItemService
{
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
     * @return Item[]
     */
    public function getItems()
    {
        return $this->em->getRepository(Item::class)->findAll();
    }

    /**
     * @param Item $item
     *
     * @return Item
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createItem(Item $item)
    {
        $this->em->persist($item);
        $this->em->flush();

        return $item;
    }

    /**
     * @param Item $item
     *
     * @return Item
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateItem(Item $item)
    {
        $this->em->merge($item);
        $this->em->flush();

        return $item;
    }

    /**
     * @param Item $item
     *
     * @return Item
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteItem(Item $item)
    {
        $this->em->remove($item);
        $this->em->flush();

        return $item;
    }


}