<?php

declare(strict_types=1);

namespace App\Core\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

use App\Core\ResponseFormatter;
use App\Core\Services\RequestService;

readonly class EntityFormRequestMiddleware implements MiddlewareInterface
{
    public function __construct(
        private RequestService $requestService,
        private ResponseFormatter $responseFormatter,
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = explode('/', $request->getUri()->getPath());
        $last = end($path);
        if ($last === 'form') {
            if ($this->requestService->isAjax($request)) {
                $body = $handler->handle($request)->getBody();
                $requestBody = (array)$request->getParsedBody();
                $body->rewind();
                $form = trim($body->getContents());
                return $this->responseFormatter->asJsonModal(
                    $this->responseFactory->createResponse(),
                    [
                        'modalContent' => $form,
                        'params'       => $requestBody['params']
                    ]
                );
            }
        }
        return $handler->handle($request);
    }
}
