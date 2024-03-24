<?php

namespace App\Core\Middleware;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use App\Core\Container;
use App\Core\Contracts\User\AuthInterface;
use App\Core\Entity\Role;
use App\Core\Contracts\User\UserInterface;
use App\Core\Enum\ServerStatus;

class RoleMiddleware implements MiddlewareInterface
{
    /**
     * @var RoleMiddleware[]
     */
    private static array $_middlewares = [];


    public function __construct(private readonly Role $role)
    {
    }

    /**
     * @param string $name
     * @return RoleMiddleware
     * @throws ContainerExceptionInterface
     * @throws EntityNotFoundException
     * @throws NotFoundExceptionInterface
     */
    public static function instance(string $name): self
    {
        if (isset(self::$_middlewares[$name])) {
            return self::$_middlewares[$name];
        }
        $container = Container::get_container();
        $instance = $container
            ->get(EntityManagerInterface::class)
            ->getRepository(Role::class)
            ->findOneBy(['name' => $name]);
        if (!$instance) {
            throw new EntityNotFoundException();
        }

        self::$_middlewares[$name] = new RoleMiddleware($instance);

        return self::$_middlewares[$name];
    }

    /**
     * @return self
     * @throws EntityNotFoundException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function admin(): self
    {
        return self::instance('admin');
    }


    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $container = Container::get_container();
        $user = $request->getAttributes()['user'] ?? null;
        /**Если вызов происходит перед вызовом AuthMiddleware дополнительно пытаемся получить*/
        if (is_null($user)) {
            $user = $container->get(AuthInterface::class)->user();
        }
        if ($user instanceof UserInterface) {
            $roles = $user->getRoles();
            foreach ($roles as $role) {
                if ($role instanceof Role) {
                    if ($role->getName() == $this->role->getName()) {
                        return $handler->handle($request);
                    }
                }
            }
        }

        return $container
            ->get(ResponseFactoryInterface::class)
            ->createResponse()
            ->withStatus(ServerStatus::FORBIDDEN->value);
    }
}
