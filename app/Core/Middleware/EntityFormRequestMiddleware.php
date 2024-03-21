<?php

declare(strict_types=1);

namespace App\Core\Middleware;

use Throwable;

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
        $body = $request->getParsedBody();
        $fromModal = $body['formParams']['modal']?? false;
        /**При сабмите не проходим эту цепочку*/
        if (in_array('form', $path, true) && $fromModal) {
            if ($this->requestService->isAjax($request)) {
                try {
                    $body = $handler->handle($request)->getBody();
                    $requestBody = (array)$request->getParsedBody();
                    $body->rewind();
                    $form = trim($body->getContents());
                    return $this->responseFormatter->asJsonModal(
                        $this->responseFactory->createResponse(),
                        [
                            'modalId' => $requestBody['modalId'],
                            'modalContent' => $form,
                            'params'       => $requestBody['params']
                        ]
                    );
                } catch (Throwable $e) {
                    return $this->responseFormatter->asJsonModal(
                        $this->responseFactory->createResponse(),
                        [
                            'modalContent' => '<div class="alert alert-danger">' . $e->getMessage() . '</div>',
                            'params'       => ['modalTitle' => 'Ошибка']
                        ]
                    );
                }
            }
        }
        return $handler->handle($request);
    }
}
