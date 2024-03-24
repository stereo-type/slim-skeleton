<?php

declare(strict_types=1);

namespace App\Core\Services;

use Psr\Http\Message\ServerRequestInterface;

use App\Core\Contracts\SessionInterface;

readonly class RequestService
{
    public function __construct(private SessionInterface $session)
    {
    }

    public function getReferer(ServerRequestInterface $request): string
    {
        $referer = $request->getHeader('referer')[0] ?? '';

        if (!$referer) {
            return $this->session->get('previousUrl');
        }

        $refererHost = parse_url($referer, PHP_URL_HOST);

        if ($refererHost !== $request->getUri()->getHost()) {
            $referer = $this->session->get('previousUrl');
        }

        return $referer;
    }

    public function isAjax(ServerRequestInterface $request): bool
    {
        return $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }


    public function getClientIp(ServerRequestInterface $request, array $trustedProxies): ?string
    {
        $serverParams = $request->getServerParams();

        if (isset($serverParams['HTTP_X_FORWARDED_FOR'])
            && in_array($serverParams['REMOTE_ADDR'], $trustedProxies, true)) {
            $ips = explode(',', $serverParams['HTTP_X_FORWARDED_FOR']);

            return trim($ips[0]);
        }

        return $serverParams['REMOTE_ADDR'] ?? null;
    }
}
