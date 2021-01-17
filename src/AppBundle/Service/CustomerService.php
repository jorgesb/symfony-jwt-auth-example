<?php

namespace AppBundle\Service;


use AppBundle\Entity\Customer;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CustomerService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var TokenService
     */
    private $tokenService;

    /**
     * @var string
     */
    private $jwtLifeTime;

    /**
     * @var string
     */
    private $jwtSecret;

    /**
     * CustomerService constructor.
     *
     * @param EntityManager                $em
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param TokenService                 $tokenService
     * @param string                       $jwtLifeTime
     * @param string                       $jwtSecret
     */
    public function __construct(
        EntityManager $em,
        UserPasswordEncoderInterface $passwordEncoder,
        TokenService $tokenService,
        $jwtLifeTime,
        $jwtSecret
    )
    {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenService = $tokenService;
        $this->jwtLifeTime = $jwtLifeTime;
        $this->jwtSecret = $jwtSecret;
    }

    /**
     * @return Customer[]
     */
    public function getCustomers()
    {
        return $this->em->getRepository(Customer::class)->findAll();
    }

    /**
     * @param Customer $customer
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createCustomer(Customer $customer)
    {
        var_dump($customer);
        $this->em->persist($customer);
        $this->em->flush();
    }

    /**
     * @param Customer $customer
     *
     * @return Customer
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateCustomer(Customer $customer)
    {
        $this->em->merge($customer);
        $this->em->flush();

        return $customer;
    }

    /**
     * @param Customer $customer
     *
     * @return Customer
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteCustomer(Customer $customer)
    {
        $this->em->remove($customer);
        $this->em->flush();

        return $customer;
    }

    /**
     * @param array $requestData
     *
     * @return array
     */
    public function login(array $requestData)
    {
        if (empty($requestData['username']) || empty($requestData['password'])) {
            return [
                'error' => [
                    'type'       => 'errors.missing_credentials',
                    'error_code' => Response::HTTP_UNAUTHORIZED,
                ],
            ];
        }

        $customer = $this->em->getRepository(Customer::class)->findOneBy(['username' => $requestData['username']]);

        if ($customer instanceof Customer) {
            // TODO: Improvement here: check customer status
            if ($this->passwordEncoder->isPasswordValid($customer, $requestData['password'])) {
                // Prepare a token for this user.
                $jwt = $this->tokenService->createCustomerToken(
                    $customer,
                    $this->jwtLifeTime,
                    $this->jwtSecret
                );

                return ['data' => ['customer_id' => $customer->getId(), 'token' => $jwt]];
            }
        }

        return [
            'error' => [
                'type'       => 'errors.wrong_credentials',
                'error_code' => Response::HTTP_UNAUTHORIZED,
            ],
        ];
    }


}