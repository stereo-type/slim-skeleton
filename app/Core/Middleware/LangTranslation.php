<?php
/**
 * @package  ${FILE_NAME}
 * @copyright 10.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Middleware;

use App\Core\Extension\TranslationExtension;
use App\Core\Services\Translator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Views\Twig;

readonly class LangTranslation implements MiddlewareInterface
{


    public function __construct(private Twig $twig, private Translator $translator)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        $this->twig->addExtension(new TranslationExtension($this->translator));
        $request = $request->withAttribute('translator', $this->translator);

        return $handler->handle($request);
    }
}