<?php

declare(strict_types=1);

namespace App\Core\Middleware;

use App\Core\Contracts\EntityManagerServiceInterface;
use App\Core\Contracts\User\AuthInterface;
use App\Core\Enum\ServerStatus;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

readonly class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private AuthInterface $auth,
        private Twig $twig,
        private EntityManagerServiceInterface $entityManagerService
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($user = $this->auth->user()) {
            $route = RouteContext::fromRequest($request)->getRoute();
            if (!is_null($route)) {
                $this->twig->getEnvironment()->addGlobal('auth', ['id' => $user->getId(), 'name' => $user->getName()]);
                $this->twig->getEnvironment()->addGlobal(
                    'current_route',
                    $route->getName()
                );

                $this->entityManagerService->enableUserAuthFilter($user->getId());

                return $handler->handle($request->withAttribute('user', $user));
            }
            throw new RuntimeException('Route not found');
        }

        return $this->responseFactory->createResponse(ServerStatus::REDIRECT->value)->withHeader('Location', '/login');
    }
}
