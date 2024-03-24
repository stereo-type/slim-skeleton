<?php

declare(strict_types = 1);

namespace App\Core;

use App\Core\Enum\ServerStatus;
use Closure;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

readonly class Csrf
{
    public function __construct(private ResponseFactoryInterface $responseFactory)
    {
    }

    public function failureHandler(): Closure
    {
        return fn(
            ServerRequestInterface $request,
            RequestHandlerInterface $handler
        ) => $this->responseFactory->createResponse()->withStatus(ServerStatus::UNAUTHORIZED->value);
    }
}
