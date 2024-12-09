<?php

namespace Zaengle\LaravelSecurityNotifications\Objects;

use ArrayObject;
use InvalidArgumentException;
use ReflectionClass;

class IPAddressHandlerObject extends ArrayObject
{
    public function __construct(public array $input)
    {
        $this->validate($input);

        parent::__construct($input);
    }

    private function validate(array $input): void
    {
        $handlerParams = collect(
            (new ReflectionClass(config('security-notifications.ip_address_driver')))
                ->getMethod('__construct')
                ->getParameters()
        );

        // Check for invalid keys
        $paramNames = $handlerParams
            ->map(fn ($param) => $param->getName())
            ->toArray();

        foreach ($input as $key => $value) {
            if (! in_array($key, $paramNames)) {
                throw new InvalidArgumentException("Invalid key: $key");
            }
        }

        // Check for missing required keys
        $requiredParams = $handlerParams
            ->reject(fn ($param) => $param->isOptional())
            ->map(fn ($param) => $param->getName())
            ->toArray();

        foreach ($requiredParams as $paramName) {
            if (! array_key_exists($paramName, $input)) {
                throw new InvalidArgumentException("Missing required key: $paramName");
            }
        }

        // Check for invalid types
        $paramTypes = $handlerParams
            ->mapWithKeys(fn ($param) => [$param->getName() => $param->getType()->getName()])
            ->toArray();

        foreach ($input as $key => $value) {
            $type = match ($paramTypes[$key]) {
                'int' => 'integer',
                'bool' => 'boolean',
                default => $paramTypes[$key],
            };

            if ($type !== gettype($value)) {
                throw new InvalidArgumentException("Invalid type for key: $key");
            }
        }
    }
}