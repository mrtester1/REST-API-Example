<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\GetGroupsList;
use App\Service\CreateUser;
use App\Service\EditUser;
use App\Service\GetUser;
use App\Service\NotAllowed;

class ServiceFactory {

    private $entityManager;
    private $requestStack;

    public function __construct(EntityManagerInterface $e, RequestStack $r)
    {
        $this->entityManager = $e;
        $this->requestStack = $r;
    }

    public function getGroupsListService() :ApiHandlerInterface
    {
        return new GetGroupsList($this->entityManager, $this->requestStack);
    }

    public function getCreateUserService() :ApiHandlerInterface
    {
        return new CreateUser($this->entityManager, $this->requestStack);
    }

    public function getUpdateUserService() :ApiHandlerInterface
    {
        return new EditUser($this->entityManager, $this->requestStack);
    }

    public function getViewUserService() :ApiHandlerInterface
    {
        return new GetUser($this->entityManager, $this->requestStack);
    }

    public function getNotAllowedService() :ApiHandlerInterface
    {
        return new NotAllowed();
    }

    public function getService() : ?ApiHandlerInterface
    {
        $url = $this->requestStack->getCurrentRequest()->getPathInfo();
        if (strpos($url, '/api/v1/group_list') !== false) {
            return $this->getGroupsListService();
        }
        if (strpos($url, '/api/v1/user_add') !== false) {
            return $this->getCreateUserService();
        }
        if (strpos($url, '/api/v1/user_update') !== false) {
            return $this->getUpdateUserService();
        }
        if (strpos($url, '/api/v1/user_get-by-id/') !== false) {
            return $this->getViewUserService();
        }
        return $this->getNotAllowedService();
    }
}