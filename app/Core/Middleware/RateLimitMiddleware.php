<?php

declare(strict_types=1);

namespace App\Core\Middleware;

use App\Core\Config;
use App\Core\Enum\ServerStatus;
use App\Core\Services\RequestService;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;
use Slim\Routing\RouteContext;
use Symfony\Component\RateLimiter\RateLimiterFactory;

readonly class RateLimitMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private RequestService $requestService,
        private Config $config,
        private RateLimiterFactory $rateLimiterFactory
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $clientIp = $this->requestService->getClientIp($request, $this->config->get('trusted_proxies'));
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        if (!is_null($route)) {
            $limiter = $this->rateLimiterFactory->create($route->getName().'_'.$clientIp);

            if ($limiter->consume()->isAccepted() === false) {
                return $this->responseFactory->createResponse(ServerStatus::TO_MANY_REQUESTS->value, 'Too many requests');
            }

            return $handler->handle($request);
        }
        throw new RuntimeException('Route not found');
    }
}
