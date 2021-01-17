<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Item;
use AppBundle\Form\ItemType;
use AppBundle\Service\ItemService;
use AppBundle\Service\SerializerService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Item controller.
 *
 * @Route("/api/v1/items")
 */
class ItemController extends BaseController
{
    /**
     * @var ItemService
     */
    private $itemService;

    public function __construct(ItemService $itemService, SerializerService $serializerService)
    {
        parent::__construct($serializerService);
        $this->itemService = $itemService;
    }

    /**
     * Lists all items.
     *
     * @Route(path="/", methods={"GET"})
     */
    public function indexAction()
    {
        $items = $this->itemService->getItems();

        return $this->createApiResponse(['data' => $items]);
    }

    /**
     * @Route(path="/item", methods={"POST"})
     * @param Request $request
     *
     * @Security("has_role('ROLE_CUSTOMER')")
     *
     * @return Response
     */
    public function createItemAction(Request $request)
    {
        $item = new Item();
        $form = $this->createForm(ItemType::class, $item);
        $form->submit($request->request->all());

        if ($form->isSubmitted() && false === $form->isValid()) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, "errors.items.wrong_parameters");
        }

        try {
            $item = $this->itemService->createItem($item);
        } catch (\Exception $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, "errors.items.unexpected_error");
        }

        return $this->createApiResponse(['data' => $item]);
    }


    /**
     * @Route(path="/{id}", methods={"PUT"})
     * @param Item    $item
     * @param Request $request
     *
     * @Security("has_role('ROLE_CUSTOMER')")
     *
     * @return Response
     */
    public function updateItemAction(Item $item, Request $request)
    {
        $form = $this->createForm(ItemType::class, $item);

        $form->submit($request->request->all());

        if ($form->isSubmitted() && false === $form->isValid()) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, "Wrong parameters");
        }

        try {
            $item = $this->itemService->updateItem($item);
        } catch (\Exception $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, "errors.items.unexpected_error");
        }


        return $this->createApiResponse(['data' => $item]);
    }

    /**
     * @Route(path="/{id}", methods={"GET"})
     * @param Item $item
     *
     * @return Response
     */
    public function getItemAction(Item $item)
    {
        return $this->createApiResponse(['data' => $item]);
    }

    /**
     * @Route(path="/{id}", methods={"DELETE"})
     * @param Item $item
     *
     * @Security("has_role('ROLE_CUSTOMER')")
     *
     * @return Response
     */
    public function deleteCustomer(Item $item)
    {
        try {
            $this->itemService->deleteItem($item);
        } catch (\Exception $exception) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, "errors.items.cannot_be_removed_error");
        }

        return $this->createApiResponse(['data' => ['success' => true]]);
    }
}
