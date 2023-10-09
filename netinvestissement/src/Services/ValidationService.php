<?php

namespace App\Services;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationService
{
    public function __construct(private readonly ValidatorInterface $validator)
    {
    }

    /**
     *  Perform entity validation using the ValidatorInterface.
     *
     * @param object $object
     * @param array|null $groups
     * @throws BadRequestHttpException
     */
    public function performEntityValidation(object $object, ?array $groups = null): void
    {
        $errors = $this->validator->validate($object, null, $groups);

        $messages = [];
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $messages[] = $error->getMessage();
            }

            throw new BadRequestHttpException(json_encode($messages), null, 400);
        }
    }

}
