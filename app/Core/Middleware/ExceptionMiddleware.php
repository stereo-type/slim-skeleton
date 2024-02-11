<?php

declare(strict_types=1);

namespace App\Core\Middleware;

use App\Core\Config;
use App\Core\Constants\ServerStatus;
use App\Core\Contracts\SessionInterface;
use App\Core\Enum\AppEnvironment;
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
        private readonly ResponseFormatter $responseFormatter,
        private readonly Config $config,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (ValidationException $e) {
            /**Обрабатывается отдельно в ValidationExceptionMiddleware*/
            return $handler->handle($request);
        } catch (Throwable $e) {
            $response = $this->responseFactory->createResponse();

            if ($this->requestService->isAjax($request)) {
                $data = ['message' => $e->getMessage()];
                if ($this->config->get('display_error_details')
                    && AppEnvironment::isDevelopment($this->config->get('app_environment'))) {
                    $data['line'] = $e->getLine();
                    $data['code'] = $e->getCode();
                    $data['file'] = $e->getFile();
                    $data['trace'] = $e->getTrace();
                }
                return $this->responseFormatter->asJson($response->withStatus(ServerStatus::BAD_REQUEST), $data);
            }

            return $response->withStatus(ServerStatus::BAD_REQUEST);
        }
    }
}
