<?php
/**
 * @package  ${FILE_NAME}
 * @copyright 10.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Middleware;

use App\Core\Config;
use App\Core\Extension\TranslationExtension;
use App\Core\Services\Translator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Views\Twig;

class LangTranslation implements MiddlewareInterface
{


    public function __construct(private readonly Config $config, private readonly Twig $twig)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Получаем язык из параметров запроса или используем язык по умолчанию
        $language = $request->getQueryParams()['lang'] ?? $this->config->get('lang');

        // Задаем язык приложения и регистрируем расширение twig
        $translator = new Translator($language);
        $this->twig->addExtension(new TranslationExtension($translator));

        // Добавляем переводчик в атрибуты запроса для использования в других обработчиках
        $request = $request->withAttribute('translator', $translator);

        return $handler->handle($request);
    }
}