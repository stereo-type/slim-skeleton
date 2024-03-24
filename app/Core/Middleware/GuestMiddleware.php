<?php

declare(strict_types = 1);

namespace App\Core\Middleware;

use App\Core\Contracts\SessionInterface;
use App\Core\Enum\ServerStatus;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

readonly class GuestMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private SessionInterface $session
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->session->get('user')) {
            return $this->responseFactory->createResponse(ServerStatus::REDIRECT->value)->withHeader('Location', '/');
        }

        return $handler->handle($request);
    }
}
