<?php
namespace App\Service;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Users;
use Symfony\Component\HttpFoundation\JsonResponse;

class GetUser implements ApiHandlerInterface {
    private $entityManager;
    private $requestStack;
    private $checker;
    private $validator;

    public function __construct(EntityManagerInterface $e, RequestStack $r)
    {
        $this->entityManager = $e;
        $this->requestStack = $r;
        $this->checker = new AccessChecker($this->entityManager, $this->requestStack);
        $this->validator = new ItemsValidator($e, $r);
    }

    private function getUser(string $id)
    {
        $user = $this->entityManager
            ->getRepository(Users::class)
            ->findOneBy(['id' => $id]);
        return $user->toArray();
    }

    private function getOutput() :array
    {
        $res = $this->checker->isValid();
        $url = $this->requestStack->getCurrentRequest()->getPathInfo();
        $id = Null;
        $matches = [];
        if (preg_match('/([a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12})/', $url, $matches)) {
            $id = $matches[1];
        }
        if (is_null($id)) {
            return [ 'code' => 414, 'message' => [ 'message' => 'User ID is invalid' ] ];
        }
        if (!$this->validator->isValidUser($id)) {
            return [ 'code' => 404, 'message' => [ 'message' => 'User ID not found' ] ];
        }
        if (($res['code'] >= 200) && ($res['code'] < 300)) {
            return [ 'code' => $res['code'], 'message' => $this->getUser($id) ];
        } else {
            return [ 'code' => $res['code'], 'message' => [ 'message' => $res['message'] ] ];
        }
    }

    public function getResponse() :JsonResponse
    {
        $output = $this->getOutput();
        return new JsonResponse($output['message'], $output['code']);
    }
}