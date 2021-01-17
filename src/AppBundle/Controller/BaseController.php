<?php

namespace AppBundle\Controller;

use AppBundle\Service\SerializerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends AbstractController
{
    /**
     * @var SerializerService
     */
    protected $serializer;


    public function __construct(SerializerService $serializer)
    {

        $this->serializer = $serializer;
    }

    protected function createApiResponse($data, $statusCode = Response::HTTP_OK)
    {
        $json = $this->serialize($data);

        return Response::create($json, $statusCode, ['Content-Type' => 'application/json']);
    }

    private function serialize($data)
    {
        return $this->serializer->serialize($data, 'json');
    }

}