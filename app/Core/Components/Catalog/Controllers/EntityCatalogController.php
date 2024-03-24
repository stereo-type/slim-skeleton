<?php

declare(strict_types=1);

namespace App\Core\Components\Catalog\Controllers;

use App\Core\Components\Catalog\Model\Filter\TableQueryParams;
use App\Core\Components\Catalog\Providers\CatalogDataProviderInterface;
use App\Core\Components\Catalog\Providers\CatalogFilterInterface;
use App\Core\Components\Catalog\Providers\CatalogFormInterface;
use App\Core\Enum\ServerStatus;
use App\Core\Exception\ValidationException;
use App\Core\Services\RequestConvertor;
use App\Core\Services\RequestService;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ServerRequestInterface as SlimRequest;
use Slim\Routing\RouteCollectorProxy;
use Symfony\Component\Form\FormFactoryInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;


abstract class EntityCatalogController extends CatalogController
{

    public const FORM_TEMPLATE = 'catalog/edit_form_layout.twig';
    public const FORM_TEMPLATE_AJAX = 'catalog/edit_form.twig';

    protected readonly RequestConvertor $requestConvertor;
    private readonly RequestService $requestService;

    public function __construct(
        CatalogDataProviderInterface&CatalogFilterInterface&CatalogFormInterface $dataProvider,
        ContainerInterface $container,
    ) {
        parent::__construct($dataProvider, $container);
        $this->requestConvertor = $this->container->get(RequestConvertor::class);
        $this->requestService = $this->container->get(RequestService::class);
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
                    $container,
                    $params
                );
                $implements = class_implements($provider);
                if (!in_array(CatalogDataProviderInterface::class, $implements) ||
                    !in_array(CatalogFilterInterface::class, $implements)) {
                    throw new InvalidArgumentException(
                        "Class $className must implements CatalogDataProviderInterface && CatalogFilterInterface"
                    );
                }
                return new static($provider, $container);
            }
        ];
    }

    protected static function additional_routes(RouteCollectorProxy $collectorProxy): void
    {
        /**create*/
        $collectorProxy->get('/form', [static::class, 'form']);
        $collectorProxy->post('/form', [static::class, 'form']);

        /**edit*/
        $collectorProxy->get('/form/{id}', [static::class, 'form']);
        $collectorProxy->post('/form/{id}', [static::class, 'form']);

        /**delete*/
        $collectorProxy->delete('/delete/{id}', [static::class, 'delete']);
    }


    /**
     * @param SlimRequest $request
     * @param Response $response
     * @param array $args
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function form(Request $request, Response $response, array $args): Response
    {
        if (!($this->dataProvider instanceof CatalogFormInterface)) {
            throw new InvalidArgumentException('DataProvider must implements CatalogFormInterface');
        }

        $args['request'] = $request->getParsedBody();
        $form = $this->dataProvider->build_form($args);

        $form->handleRequest($this->requestConvertor->requestSlimToSymfony($request));

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $success = $this->dataProvider->save_form_data($form->getData());

                if ($this->requestService->isAjax($request)) {
                    return $this->responseFormatter->asJson(
                        $response->withStatus(ServerStatus::CREATED->value),
                        ['success' => $success]
                    );
                }

                return $response->withHeader('Location', $this->get_index_route())->withStatus(
                    ServerStatus::CREATED->value
                );
            } else {
                $errors = [];
                foreach ($form->getErrors(true) as $e) {
                    $errors [$e->getOrigin()->getName()] = $e->getMessage();
                }
                throw new ValidationException($errors);
            }
        }

        $template = $this->requestService->isAjax($request) ? static::FORM_TEMPLATE_AJAX : static::FORM_TEMPLATE;

        return $this->twig->render(
            $response,
            $template,
            ['form' => $form->createView(), 'form_action' => $request->getUri()->getPath()]
        );
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        if (!($this->dataProvider instanceof CatalogFormInterface)) {
            throw new InvalidArgumentException('DataProvider must implements CatalogFormInterface');
        }

        if (!$id = (int)$args['id']) {
            throw new InvalidArgumentException('Id must specified for delete');
        }

        $success = $this->dataProvider->delete($id);

        if ($this->requestService->isAjax($request)) {
            return $this->responseFormatter->asJson(
                $response->withStatus(ServerStatus::ACCEPTED->value),
                ['success' => $success]
            );
        }

        return $response->withHeader('Location', $this->get_index_route())->withStatus(ServerStatus::ACCEPTED->value);
    }


}
