<?php

declare(strict_types = 1);

namespace App\Core\Middleware;

use App\Core\Constants\ServerStatus;
use App\Core\Contracts\SessionInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GuestMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly SessionInterface $session
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->session->get('user')) {
            return $this->responseFactory->createResponse(ServerStatus::REDIRECT)->withHeader('Location', '/');
        }

        return $handler->handle($request);
    }
}