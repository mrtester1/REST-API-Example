<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\ServiceFactory;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1/")
 */
class ApiController extends AbstractController
{
    private $serviceFactory;

    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack) {
        $this->serviceFactory = new ServiceFactory($entityManager, $requestStack);
    }

    /**
     * @Route("group_list", name="group_list", priority=0, methods={"GET"})
     */
    public function group_list(): JsonResponse
    {
        return $this->serviceFactory->getService()->getResponse();
    }

    /**
     * @Route("user_update", name="user_update", priority=0, methods={"POST"})
     */
    public function user_update(): JsonResponse
    {
        #error_log("Before user update");
        return $this->serviceFactory->getService()->getResponse();
    }

    /**
     * @Route("user_get-by-id/{id}", name="view_user", priority=0, requirements={"id"="[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}"}, methods={"GET"})
     */
    public function view_user(): JsonResponse
    {
        #error_log("Before user update");
        return $this->serviceFactory->getService()->getResponse();
    }

    /**
     * @Route("user_add", name="user_add", priority=0, methods={"POST"})
     */
    public function user_add(): JsonResponse
    {
        return $this->serviceFactory->getService()->getResponse();
    }

   /**
    * @Route("{anything}", name="error_response", defaults={"anything" = null}, priority=-1, requirements={"anything"=".+"})
    */
    public function error_response(): JsonResponse
    {
        #error_log("Before error_response");
        return $this->serviceFactory->getService()->getResponse();
    }
}