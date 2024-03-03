<?php

declare(strict_types = 1);

namespace App\Core\Middleware;

use App\Core\Contracts\SessionInterface;
use App\Core\Services\RequestService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

readonly class StartSessionsMiddleware implements MiddlewareInterface
{
    public function __construct(
        private SessionInterface $session,
        private RequestService $requestService
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->session->start();

        $response = $handler->handle($request);

        if ($request->getMethod() === 'GET' && ! $this->requestService->isAjax($request)) {
            $this->session->put('previousUrl', (string) $request->getUri());
        }

        $this->session->save();

        return $response;
    }
}
