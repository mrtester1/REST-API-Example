<?php
namespace App\Service;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Groups;
use Symfony\Component\HttpFoundation\JsonResponse;

class GetGroupsList implements ApiHandlerInterface {
    private $entityManager;
    private $requestStack;
    private $checker;

    public function __construct(EntityManagerInterface $e, RequestStack $r)
    {
        $this->entityManager = $e;
        $this->requestStack = $r;
        $this->checker = new AccessChecker($this->entityManager, $this->requestStack);
    }

    private function getList()
    {
        $res = [];
        $groups = $this->entityManager
            ->getRepository(Groups::class)
            ->findAll();
        foreach ($groups as $group) {
            $res[] = $group->toArray();
        }
        return $res;
    }

    private function getOutput() :array
    {
        $res = $this->checker->isValid();
        if (($res['code'] >= 200) && ($res['code'] < 300)) {
            return [
                'code' => $res['code'],
                'message' => $this->getList()
            ];
        } else {
            return [
                'code' => $res['code'],
                'message' => [ 'message' => $res['message'] ]
            ];
        }
    }

    public function getResponse() :JsonResponse
    {
        $output = $this->getOutput();
        return new JsonResponse($output['message'], $output['code']);
    }
}