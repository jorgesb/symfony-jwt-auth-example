<?php

namespace AppBundle\Service;

use AppBundle\Entity\Customer;
use DateTime;
use Firebase\JWT\JWT;

/**
 * Class TokenService
 */
class TokenService
{
    /**
     * {@inheritdoc}
     */
    public function createToken(array $data, $key, $lifetime)
    {
        $currentTimestamp = (new DateTime())->getTimestamp();
        $token = [
            "iat"     => $currentTimestamp, // Issued At Time
            "nbf"     => $currentTimestamp, // Not Before Time
            "exp"     => (new DateTime($lifetime))->getTimestamp(), // Expiration Time
            "payload" => $data,
        ];

        return JWT::encode($token, $key, 'HS256');
    }

    /**
     * {@inheritdoc}
     */
    public function decode($token, $key)
    {
        try {
            $decodedJWT = JWT::decode($token, $key, ['HS256']);
        } catch (\Exception $e) {
            $decodedJWT = false;
        }

        return $decodedJWT;
    }

    /**
     * {@inheritdoc}
     */
    public function createCustomerToken(Customer $customer, $lifetime, $secret)
    {
        // TODO: Here we can add another layer of security, checking other customer info
        $data = ['id' => $customer->getId()];

        return $this->createToken(
            ["customer" => $data],
            $secret,
            $lifetime
        );
    }
}