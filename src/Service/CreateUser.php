<?php
namespace App\Service;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Users;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\ItemsValidator;

class CreateUser implements ApiHandlerInterface {
    protected $entityManager;
    protected $requestStack;
    protected $checker;
    protected $validator;

    public function __construct(EntityManagerInterface $e, RequestStack $r)
    {
        $this->entityManager = $e;
        $this->requestStack = $r;
        $this->checker = new AccessChecker($this->entityManager, $this->requestStack);
        $this->validator = new ItemsValidator($e, $r);
    }

    private function addUserFromRequest() :array
    {
        $check_arr = $this->validator->validateRequest(true);
        if (($check_arr['code'] < 200) || ($check_arr['code'] > 299)) {
            return $check_arr;
        }
        $user = new Users();
        $data = $check_arr['message'];
        $user->setActive($data['active'])
             ->setDob(empty($data['dob']) ? Null : \DateTime::createFromFormat('d.m.Y', $data['dob']))
             ->setGender(empty($data['gender']) ? Null : $data['gender'])
             ->setEmail($data['email'])
             ->setUserGroup(empty($data['group']) ? ItemsValidator::DEFAULT_GROUPID : $data['group'])
             ->setFirstName($data['firstName'])
             ->setLastName($data['lastName']);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return [
            'code' => 200,
            'message' => $user->toArray()
        ];
    }

    protected function getCheckerOutput()
    {
        $res = $this->checker->isValid();
        if (($res['code'] >= 200) && ($res['code'] < 300)) {
            return $this->addUserFromRequest();
        } else {
            return [
                'code' => $res['code'],
                'message' => [ 'message' => $res['message'] ]
            ];
        }
    }

    protected function getOutput() :array
    {
        $res = $this->getCheckerOutput();
        if (($res['code'] >= 200) && ($res['code'] < 300)) {
            return [
                'code' => $res['code'],
                'message' => $res['message']
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