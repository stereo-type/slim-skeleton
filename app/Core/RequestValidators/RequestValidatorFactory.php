<?php

declare(strict_types = 1);

namespace App\Core\RequestValidators;

use App\Core\Contracts\RequestValidatorFactoryInterface;
use App\Core\Contracts\RequestValidatorInterface;
use Psr\Container\ContainerInterface;

class RequestValidatorFactory implements RequestValidatorFactoryInterface
{
    public function __construct(private readonly ContainerInterface $container)
    {
    }

    public function make(string $class): RequestValidatorInterface
    {
        $validator = $this->container->get($class);

        if ($validator instanceof RequestValidatorInterface) {
            return $validator;
        }

        throw new \RuntimeException('Failed to instantiate the request validator class "' . $class . '"');
    }
}
