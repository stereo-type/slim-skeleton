<?php
/**
 * @package  ${FILE_NAME}
 * @copyright 11.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Controllers;


use App\Core\ResponseFormatter;
use App\Core\Services\RequestService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ModalController
{


    public function __construct(
        private readonly Twig $twig,
        private readonly RequestService $requestService,
        private readonly ResponseFormatter $responseFormatter
    ) {
    }

    /**
     * @param  ServerRequestInterface  $request
     * @param  ResponseInterface  $response
     * @return ResponseInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = $request->getParsedBody();
        $modal = $this->twig->fetch(
            'modal.twig',
            [
                'modalId' => $body['modalId'] ?? rand(0, 1000),
                'modalContent' => $body['modalContent']
            ]
        );


        if ($this->requestService->isAjax($request)) {
            return $this->responseFormatter->asJson($response, ['modal' => $modal]);
        }

        $response->getBody()->write($modal);
        return $response;
    }

}