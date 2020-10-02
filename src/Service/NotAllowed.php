<?php
namespace App\Service;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotAllowed implements ApiHandlerInterface {
    const METHOD_NOT_ALLOWED = 405;
    public function getResponse() :JsonResponse
    {
        $output = [
            'code' => self::METHOD_NOT_ALLOWED,
            'message' => [ 'message' => 'Method not allowed' ]
        ];
        return new JsonResponse($output['message'], $output['code']);
    }
}