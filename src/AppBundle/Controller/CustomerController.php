<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Customer;
use AppBundle\Form\CustomerType;
use AppBundle\Service\CustomerService;
use AppBundle\Service\SerializerService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CustomerController
 * @Route("/api/v1/customers")
 */
class CustomerController extends BaseController
{
    /**
     * @var CustomerService
     */
    private $customerService;

    public function __construct(CustomerService $customerService, SerializerService $serializerService)
    {
        parent::__construct($serializerService);
        $this->customerService = $customerService;
    }

    /**
     * Lists all customers.
     *
     * @Route(path="/", methods={"GET"})
     */
    public function indexAction()
    {
        $customers = $this->customerService->getCustomers();

        return $this->createApiResponse(['data' => $customers]);
    }

    /**
     * @Route(path="/customer", methods={"POST"})
     * @param Request $request
     *
     * @return Response
     */
    public function createCustomerAction(Request $request)
    {
        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);
        $form->submit($request->request->all());

        if ($form->isSubmitted() && false === $form->isValid()) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, "errors.customers.wrong_parameters");
        }

        try {
            $this->customerService->createCustomer($customer);
        } catch (\Exception $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, "errors.customers.unexpected_error");
        }

        return $this->createApiResponse(['data' => $customer]);
    }


    /**
     * @Route(path="/{id}", methods={"PUT"})
     * @param Customer $customer
     * @param Request  $request
     *
     * @Security("has_role('ROLE_CUSTOMER')")
     *
     * @return Response
     */
    public function updateCustomerAction(Customer $customer, Request $request)
    {
        $form = $this->createForm(CustomerType::class, $customer);

        $form->submit($request->request->all());

        if ($form->isSubmitted() && false === $form->isValid()) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, "errors.customers.wrong_parameters");
        }

        try {
            $customer = $this->customerService->updateCustomer($customer);
        } catch (\Exception $exception) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, "errors.customers.unexpected_error");
        }

        return $this->createApiResponse(['data' => $customer]);
    }

    /**
     * @Route(path="/login", methods={"POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function loginMember(Request $request)
    {
        $loginResult = $this->customerService->login($request->request->all());

        if (isset($loginResult['error'])) {
            throw new HttpException($loginResult['error']['error_code'], $loginResult['error']['type']);
        }

        // Check login credentials
        return $this->createApiResponse($loginResult);
    }

    /**
     * @Route(path="/{id}", methods={"GET"})
     * @param Customer $customer
     *
     * @return Response
     */
    public function getCustomerAction(Customer $customer)
    {
        return $this->createApiResponse(['data' => $customer]);
    }

    /**
     * // TODO: For E-commerce might be better do just a logic deletion, i.e: changing customer status to archived
     * @Route(path="/{id}", methods={"DELETE"})
     * @param Customer $customer
     *
     * @Security("has_role('ROLE_CUSTOMER')")
     *
     * @return Response
     */
    public function deleteCustomer(Customer $customer)
    {
        try {
            $this->customerService->deleteCustomer($customer);
        } catch (\Exception $exception) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, "errors.customers.unexpected_error");
        }

        return $this->createApiResponse(['data' => ['success' => true ]]);
    }
}