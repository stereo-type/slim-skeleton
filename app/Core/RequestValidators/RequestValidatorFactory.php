<?php

declare(strict_types = 1);

namespace App\Core\RequestValidators;

use App\Core\Contracts\RequestValidatorFactoryInterface;
use App\Core\Contracts\RequestValidatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

readonly class RequestValidatorFactory implements RequestValidatorFactoryInterface
{
    public function __construct(private ContainerInterface $container)
    {
    }

    /**
     * @param  string  $class
     * @return RequestValidatorInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function make(string $class): RequestValidatorInterface
    {
        $validator = $this->container->get($class);

        if ($validator instanceof RequestValidatorInterface) {
            return $validator;
        }

        throw new RuntimeException('Failed to instantiate the request validator class "' . $class . '"');
    }
}
