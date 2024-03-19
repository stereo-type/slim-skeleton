<?php
/**
 * @package  ${FILE_NAME}
 * @copyright 11.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\Core\ResponseFormatter;
use App\Core\Services\RequestService;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

readonly class ModalController
{


    public function __construct(
        private RequestService $requestService,
        private ResponseFormatter $responseFormatter
    ) {
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = (array)$request->getParsedBody();
        if ($this->requestService->isAjax($request)) {
            return $this->responseFormatter->asJsonModal($response, $body);
        }
        return $this->responseFormatter->asModal($response, $body);
    }

}