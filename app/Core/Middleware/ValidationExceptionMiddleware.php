<?php

declare(strict_types = 1);

namespace App\Core\Middleware;

use App\Core\Contracts\SessionInterface;
use App\Core\Enum\ServerStatus;
use App\Core\Exception\ValidationException;
use App\Core\ResponseFormatter;
use App\Core\Services\RequestService;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

readonly class ValidationExceptionMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private SessionInterface $session,
        private RequestService $requestService,
        private ResponseFormatter $responseFormatter
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (ValidationException $e) {
            $response = $this->responseFactory->createResponse();

            if ($this->requestService->isAjax($request)) {
                return $this->responseFormatter->asJson($response->withStatus(ServerStatus::VALIDATION_ERROR->value), $e->errors);
            }

            $referer  = $this->requestService->getReferer($request);
            $oldData  = $request->getParsedBody();

            $sensitiveFields = ['password', 'confirmPassword'];

            $this->session->flash('errors', $e->errors);
            $this->session->flash('old', array_diff_key($oldData, array_flip($sensitiveFields)));

            return $response->withHeader('Location', $referer)->withStatus(ServerStatus::REDIRECT->value);
        }
    }
}
