<?php
namespace App\Service;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Users;

class EditUser extends CreateUser implements ApiHandlerInterface {
    private function editUserFromRequest() :array
    {
        $check_arr = $this->validator->validateRequest(false);
        if (($check_arr['code'] < 200) || ($check_arr['code'] > 299)) {
            return $check_arr;
        }
        $user = $this->entityManager
            ->getRepository(Users::class)
            ->findOneBy(['id' => $check_arr['message']['id']]);

        $user->fromArray($check_arr['message']);

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
            return $this->editUserFromRequest();
        } else {
            return [
                'code' => $res['code'],
                'message' => [ 'message' => $res['message'] ]
            ];
        }
    }

}