<?php

declare(strict_types = 1);

namespace App\Core\Controllers;

use App\Core\ResponseFormatter;
use DateTime;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class HomeController
{
    public function __construct(
        private readonly Twig $twig,
    ) {
    }

    /**
     * @param  Response  $response
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index(Response $response): Response
    {
        $startDate             = DateTime::createFromFormat('Y-m-d', date('Y-m-01'));
        $endDate               = new DateTime('now');

        return $this->twig->render(
            $response,
            'dashboard.twig',
            [
                'totals'                => 1,
                'transactions'          => 1,
                'topSpendingCategories' => 1,
            ]
        );
    }

}
