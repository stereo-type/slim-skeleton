<?php

namespace App\Core\Services;

use Psr\Http\Message\ServerRequestInterface as SlimRequest;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;

readonly class RequestConvertor
{
    public function requestSlimToSymfony(SlimRequest $req): SymfonyRequest
    {
        $httpFoundationFactory = new HttpFoundationFactory();
        return $httpFoundationFactory->createRequest($req);
    }

}