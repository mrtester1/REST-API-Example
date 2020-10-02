<?php

namespace App\Service;
use App\Service\ItemsValidator;
use App\Entity\Groups;
use App\Entity\Acl;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;

class AccessChecker
{
    private $entityManager;
    private $requestStack;
    private $itemsValidator;

    public function __construct(EntityManagerInterface $e, RequestStack $r)
    {
        $this->entityManager = $e;
        $this->requestStack = $r;
        $this->itemsValidator = new ItemsValidator($e, $r);
    }

    public function isValidGroup() :bool {
        $current_request = $this->requestStack->getCurrentRequest();
        $group = (string)$current_request->headers->get('X-User-Group');
        return $this->itemsValidator->isValidGroup($group);
    }

    public function hasAccess() :bool {
        $current_request = $this->requestStack->getCurrentRequest();
        $group = (string)$current_request->headers->get('X-User-Group');
        return $this->entityManager
            ->getRepository(Acl::class)
            ->hasAccess($group, $current_request->getPathInfo(), $current_request->getMethod());
    }

    public function isValid(): array {
        $group = (string)$this->requestStack->getCurrentRequest()->headers->get('X-User-Group');

        if (!$this->itemsValidator->isValidUuid($group)) {
            return [
                'code' => 405,
                'message' => 'Access group is invalid UUID'
            ];
        }

        if (!$this->isValidGroup()) {
            return [
                'code' => 403,
                'message' => 'Access group is invalid'
            ];
        }

        if (!$this->hasAccess()) {
            return [
                'code' => 403,
                'message' => 'Access forbidden'
            ];
        }

        return [
            'code' => 200,
            'message' => 'OK'
        ];
    }
}