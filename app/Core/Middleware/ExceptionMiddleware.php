<?php

declare(strict_types = 1);

namespace App\Core\Middleware;

use App\Core\Constants\ServerStatus;
use App\Core\Contracts\SessionInterface;
use App\Core\Exception\ValidationException;
use App\Core\ResponseFormatter;
use App\Core\Services\RequestService;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class ExceptionMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly RequestService $requestService,
        private readonly ResponseFormatter $responseFormatter
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (Throwable $e) {
            $response = $this->responseFactory->createResponse();

            if ($this->requestService->isXhr($request)) {
                return $this->responseFormatter->asJson($response->withStatus(ServerStatus::BAD_REQUEST), [$e->getMessage()]);
            }

            $referer  = $this->requestService->getReferer($request);
            return $response->withHeader('Location', $referer)->withStatus(ServerStatus::REDIRECT);
        }
    }
}
