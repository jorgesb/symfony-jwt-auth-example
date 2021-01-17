<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Customer;
use AppBundle\Entity\CustomerOrder;
use AppBundle\Entity\Item;
use AppBundle\Service\CustomerOrderService;
use AppBundle\Service\SerializerService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class OrderController
 * @Route("/api/v1/orders")
 */
class OrderController extends BaseController
{
    /**
     * @var CustomerOrderService
     */
    private $customerOrderService;

    public function __construct(CustomerOrderService $customerOrderService, SerializerService $serializerService)
    {
        parent::__construct($serializerService);
        $this->customerOrderService = $customerOrderService;
    }

    /**
     * Lists all orders from the logged customer.
     *
     * @Route(path="/", methods={"GET"})
     * @Security("has_role('ROLE_CUSTOMER')")
     *
     */
    public function indexAction()
    {
        /** @var Customer $customer */
        $customer = $this->getUser();
        $orders = $this->customerOrderService->getCustomerOrders($customer->getId());

        return $this->createApiResponse(['data' => $orders]);
    }

    /**
     * @Route(path="/order", methods={"POST"})
     *
     * @Security("has_role('ROLE_CUSTOMER')")
     *
     * @return Response
     */
    public function createCustomerOrderAction()
    {
        /** @var Customer $customer */
        $customer = $this->getUser();
        $customerOrder = new CustomerOrder();
        $customerOrder->setCustomer($customer);

        try {
            $customerOrder = $this->customerOrderService->createCustomerOrder($customerOrder);
        } catch (\Exception $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, "errors.orders.cannot_create_order");
        }

        return $this->createApiResponse(['data' => $customerOrder]);
    }

    /**
     * @Route(path="/{id}/add-item/{item}", methods={"POST"})
     *
     * @Security("has_role('ROLE_CUSTOMER')")
     *
     * @param CustomerOrder $order
     * @param Item          $item
     * @param Request       $request
     *
     * @return Response
     */
    public function addItemToCustomerOrderAction(CustomerOrder $order, Item $item, Request $request)
    {
        $amount = $request->request->get('amount', null);

        try {
            $customerOrder = $this->customerOrderService->createCustomerOrderItem($order, $item, $amount);
        } catch (\Exception $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, "errors.orders.cannot_add_item");
        }

        // TODO: Improvement: Create a query to calculate order total price
        // $orderTotal = $this->customerOrderService->getOrderTotalPrice($customerOrder);

        return $this->createApiResponse(['data' => $customerOrder]);
    }

    /**
     * @Route(path="/{id}/remove-item/{item}", methods={"DELETE"})
     *
     * @Security("has_role('ROLE_CUSTOMER')")
     *
     * @param CustomerOrder $order
     * @param Item          $item
     *
     * @return Response
     */
    public function removeItemFromCustomerOrderAction(CustomerOrder $order, Item $item)
    {
        try {
            $customerOrder = $this->customerOrderService->deleteCustomerOrderItem($order, $item);
        } catch (\Exception $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, "errors.orders.cannot_add_item");
        }

        // TODO: Improvement: Create a query to calculate order total price
        // $orderTotal = $this->customerOrderService->getOrderTotalPrice($customerOrder);

        return $this->createApiResponse(['data' => $customerOrder]);
    }

    /**
     * @Route(path="/{id}", methods={"GET"})
     * @param CustomerOrder $customerOrder
     *
     * @return Response
     */
    public function getCustomerOrderAction(CustomerOrder $customerOrder)
    {
        // TODO: Calculate total price and return it on the output
        return $this->createApiResponse(['data' => $customerOrder]);
    }

    /**
     * // TODO: For E-commerce might be better do just a logic deletion, i.e: changing order status to archived
     * @Route(path="/{id}", methods={"DELETE"})
     *
     * @param CustomerOrder $customerOrder
     *
     * @return Response
     */
    public function deleteCustomerOrder(CustomerOrder $customerOrder)
    {
        try {
            $this->customerOrderService->deleteCustomerOrder($customerOrder);
        } catch (\Exception $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, "errors.orders.cannot_be_deleted");
        }

        return $this->createApiResponse(['data' => ['success' => true ]]);
    }
}