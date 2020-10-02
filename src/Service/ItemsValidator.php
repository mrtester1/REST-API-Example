<?php
namespace App\Service;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;
use App\Entity\Groups;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ItemsValidator {

    private $validator;
    private $entityManager;
    private $requestStack;
    const DEFAULT_GROUPID = 'd18b29bd-b4ef-4891-98d3-aa25ccc6e9a9';

    private function nullifyIfNotSet(array $data)
    {
        $fieldsToNullify = ['group', 'firstName', 'lastName', 'gender', 'dob', 'email', 'active'];
        $return = $data;
        foreach ($fieldsToNullify as $key) {
            if (!isset($return[$key])) {
                if ($key === 'group') {
                    $return[$key] = self::DEFAULT_GROUPID;
                } else {
                    $return[$key] = Null;
                }
            }
        }
        return $return;
    }

    public function __construct(EntityManagerInterface $e, RequestStack $r) {
        $this->validator = Validation::createValidator();
        $this->entityManager = $e;
        $this->requestStack = $r;
    }

    public function isValidUuid(?string $field): bool
    {
        $uuidContraint = new Assert\Uuid();
        $errors = $this->validator->validate(
            $field,
            $uuidContraint
        );
        if (count($errors) !== 0)
        {
            return false;
        }
        return true;
    }

    public function isValidGroup(?string $field): bool
    {
        if (!$this->isValidUuid($field)) {
            return false;
        }

        return $this->entityManager
            ->getRepository(Groups::class)
            ->isValidGroup($field);
    }

    public function isValidUser(?string $field): bool
    {
        if (!$this->isValidUuid($field)) {
            return false;
        }

        return $this->entityManager
            ->getRepository(Users::class)
            ->isValidUser($field);
    }

    public function isValidEmail(?string $field): bool
    {
        $emailConstraint = new Assert\Email();
        $errors = $this->validator->validate(
            $field,
            $emailConstraint
        );
        if (count($errors) !== 0)
        {
            return false;
        }
        return true;
    }

    public function isValidRuDate(?string $field): bool
    {
        if (mb_strlen($field, 'UTF-8') !== 10) {
            return false;
        }

        $chunks = explode('.', $field);

        if (count($chunks) !== 3) {
            return false;
        }

        if (!checkdate($chunks[1], $chunks[0], $chunks[2]))
        {
            return false;
        }
        return true;
    }

    public function isValidGender(?string $field): bool
    {
        if ($field === "") {
            return true;
        }
        if (in_array($field, ['F', 'M'])) {
            return true;
        }
        return false;
    }

    public function isValidJson(?string $field): bool
    {
        $jsonContraint = new Assert\Json();
        $errors = $this->validator->validate(
            $field,
            $jsonContraint
        );
        if (count($errors) !== 0)
        {
            return false;
        }
        return true;
    }

    private function isValidEmailValue(array &$data, bool $isInsert) : bool
    {
        if ($isInsert) {
            return $this->isValidEmail($data['email']);
        } else {
            if (isset($data['email'])) {
                return $this->isValidEmail($data['email']);
            }
            return true;
        }
    }

    private function isValidActiveValue(array &$data, bool $isInsert) : bool
    {
        if ($isInsert) {
            return is_bool($data['active']);
        } else {
            if (isset($data['active'])) {
                return is_bool($data['active']);
            }
            return true;
        }
    }

    private function isValidGenderValue(array &$data, bool $isInsert) : bool
    {
        if ($isInsert) {
            if (is_null($data['gender'])) {
                return true;
            }
            return in_array($data['gender'], array('F', 'M'));
        } else {
            if (isset($data['active'])) {
                return in_array($data['gender'], array('F', 'M'));
            }
            return true;
        }
    }

    private function isValidRuDateValue(array &$data, bool $isInsert) : bool
    {
        if ($isInsert) {
            if (is_null($data['dob'])) {
                return true;
            }
            return $this->isValidRuDate($data['dob']);
        } else {
            if (isset($data['dob'])) {
                return $this->isValidRuDate($data['dob']);
            }
            return true;
        }
    }

    private function requestToArray(bool $isInsert) :array
    {
        $raw = $this->requestStack->getCurrentRequest()->getContent();
        if (!$this->isValidJson($raw)) {
            return [ 'code' => 406, 'message' => 'JSON is invalid'];
        }
        if ($isInsert) {
            $data = $this->nullifyIfNotSet(json_decode($raw, true));
        } else {
            $data = json_decode($raw, true);
        }
        return $data;
    }

    private function validateMandatoryFields(array &$data, bool $isInsert) :array
    {
        if (!$this->isValidEmailValue($data, $isInsert)) {
            return [ 'code' => 407, 'message' => '`Email` is invalid' ];
        }
        if (!$this->isValidActiveValue($data, $isInsert)) {
            return [ 'code' => 408, 'message' => '`active` field is invalid (not boolean)' ];
        }
        if (!$isInsert && !isset($data['id'])) {
            return [ 'code' => 402, 'message' => 'The field `id` must present for update operation' ];
        }
        if ((isset($data['id']) && !$this->isValidUser($data['id']))) {
            return [ 'code' => 413, 'message' => '`id` field is invalid (must be null or valid uuid)'];
        }
        return [ 'code' => 200, 'message' => $data ];
    }

    private function validateOptionalFields(array &$data, bool $isInsert) :array
    {
        if (!$this->isValidGenderValue($data, $isInsert)) {
            return [ 'code' => 410, 'message' => '`gender` field is invalid (not in (Null, `F`, `M`))'];
        }
        if (!$this->isValidRuDateValue($data, $isInsert)) {
            return [ 'code' => 411, 'message' => '`dob` field is invalid (must be null or dd.mm.yyyy)'];
        }
        if (isset($data['group']) && !$this->isValidGroup($data['group'])) {
            return [ 'code' => 412, 'message' => '`group` field is invalid (must be null or valid uuid among well known groups)'];
        }
        return [ 'code' => 200, 'message' => $data ];
    }

    public function validateRequest(bool $isInsert) :array
    {
        $data = $this->requestToArray($isInsert);
        $ret = $this->validateMandatoryFields($data, $isInsert);
        if ($ret['code'] !== 200) {
            return $ret;
        }
        $ret = $this->validateOptionalFields($data, $isInsert);
        if ($ret['code'] !== 200) {
            return $ret;
        }
        return [ 'code' => 200, 'message' => $data ];
    }
}