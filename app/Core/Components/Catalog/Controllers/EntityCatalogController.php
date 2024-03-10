<?php

declare(strict_types=1);

namespace App\Core\Components\Catalog\Controllers;

use App\Core\Services\RequestConvertor;
use InvalidArgumentException;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Psr\Http\Message\ServerRequestInterface as SlimRequest;
use Slim\Views\Twig;
use Slim\Routing\RouteCollectorProxy;
use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;

use App\Core\ResponseFormatter;
use App\Core\Constants\ServerStatus;
use App\Core\Contracts\SessionInterface;
use App\Core\Components\Catalog\Model\Filter\TableQueryParams;
use App\Core\Components\Catalog\Providers\CatalogDataProviderInterface;
use App\Core\Components\Catalog\Providers\CatalogFilterInterface;
use App\Core\Components\Catalog\Providers\CatalogFormInterface;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

abstract class EntityCatalogController extends CatalogController
{

    public const FORM_TEMPLATE = 'catalog/edit_form.twig';

    public function __construct(
        CatalogDataProviderInterface&CatalogFilterInterface&CatalogFormInterface $dataProvider,
        Twig $twig,
        ResponseFormatter $responseFormatter,
        SessionInterface $session,
        protected readonly RequestConvertor $requestConvertor
    ) {
        parent::__construct($dataProvider, $twig, $responseFormatter, $session);
    }


    /**Метод обертка для упрощенного биднига в контейнере
     * @param string $className
     * @param TableQueryParams|null $params
     * @return mixed
     */
    public static function binding(string $className, ?TableQueryParams $params = null): array
    {
        return [
            static::class => static function (ContainerInterface $container) use ($className, $params) {
                $provider = new $className(
                    $container->get(EntityManagerInterface::class),
                    $container->get(FormFactoryInterface::class),
                    $params
                );
                $implements = class_implements($provider);
                if (!in_array(CatalogDataProviderInterface::class, $implements) || !in_array(
                        CatalogFilterInterface::class,
                        $implements
                    )) {
                    throw new InvalidArgumentException(
                        "Class $className must implements CatalogDataProviderInterface && CatalogFilterInterface"
                    );
                }
                return new static(
                    $provider,
                    $container->get(Twig::class),
                    $container->get(ResponseFormatter::class),
                    $container->get(SessionInterface::class),
                    $container->get(RequestConvertor::class),
                );
            }
        ];
    }

    protected static function additional_routes(RouteCollectorProxy $collectorProxy): void
    {
        $collectorProxy->get('/form', [static::class, 'form']);
    }


    private function request_slim_to_symfony(SlimRequest $req): SymfonyRequest
    {
        return $this->requestConvertor->requestSlimToSymfony($req);
    }


    public function form(Request $request, Response $response): Response
    {
        if (!($this->dataProvider instanceof CatalogFormInterface)) {
            throw new InvalidArgumentException('DataProvider must implements CatalogFormInterface');
        }

        $form = $this->dataProvider->form();

        $form->handleRequest($this->request_slim_to_symfony($request));



        // Проверка валидности формы
        if ($form->isSubmitted() && $form->isValid()) {
            // Обработка данных формы, например, сохранение в базу данных
            $data = $form->getData();

            return $response->withHeader('Location', $this->get_index_route())->withStatus(ServerStatus::REDIRECT);
        }

        return $this->twig->render(
            $response,
            static::FORM_TEMPLATE,
            [
                'form' => $form->createView()
            ],
        );
    }


}
