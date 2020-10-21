<?php

namespace App\Service;

use Anyx\LoginGateBundle\Service\UsernameResolverInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Username resolver for json login.
 */
class UsernameResolver implements UsernameResolverInterface
{
    public function resolve(Request $request)
    {
        $requestData = json_decode($request->getContent(), true);

        return is_array($requestData) && array_key_exists('email', $requestData) ? $requestData['email'] : null;
    }
}
