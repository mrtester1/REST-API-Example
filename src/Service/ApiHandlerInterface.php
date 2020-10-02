<?php
namespace App\Service;
use Symfony\Component\HttpFoundation\JsonResponse;

interface ApiHandlerInterface {
    public function getResponse() :JsonResponse;
}