<?php

declare(strict_types=1);

namespace App\Core;

use Exception;
use Psr\Http\Message\ResponseInterface;

use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

readonly class ResponseFormatter
{

    public function __construct(private Twig $twig)
    {
    }

    public function asJson(
        ResponseInterface $response,
        mixed $data,
        int $flags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_APOS | JSON_THROW_ON_ERROR
    ): ResponseInterface {
        $response = $response->withHeader('Content-Type', 'application/json');

        $response->getBody()->write(json_encode($data, $flags));

        return $response;
    }

    /**
     * @param array $body
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function asJsonModal(ResponseInterface $response, array $body): ResponseInterface
    {
        $modal = $this->_modal($body);
        return $this->asJson($response, ['modal' => $modal]);
    }

    /**
     * @param array $body
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function asModal(ResponseInterface $response, array $body): ResponseInterface
    {
        $modal = $this->_modal($body);
        $response->getBody()->write($modal);
        return $response;
    }


    /**
     * @param array $body
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    private function _modal(array $body): string
    {
        $params = $body['params'] ?? [];
        $modalTitle = $params['modalTitle'] ?? '';
        $modalClass = $params['modalClasses'] ?? '';
        $modalActionType = $params['modalActionType'] ?? 'none';
        $modalTemplate = $params['modalTemplate'] ?? 'modal.twig';

        return $this->twig->fetch(
            $modalTemplate,
            [
                'modalId'         => $body['modalId'] ?? random_int(0, 1000),
                'modalContent'    => $body['modalContent'] ?? '',
                'modalTitle'      => $modalTitle,
                'modalClass'      => $modalClass,
                'modalActionType' => $modalActionType,
            ]
        );
    }
}
