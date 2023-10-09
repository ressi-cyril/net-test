<?php

namespace App\Interfaces;

interface ServiceInterface
{
    /**
     * Validate and save to DB given object
     * @param object $object
     * @param array|null $groups
     * @return void
     */
    public function validateAndSave(object $object, ?array $groups = null): void;
}