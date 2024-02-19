<?php

declare(strict_types=1);

namespace App\Core\Components\Catalog\Controllers;

use App\Core\Components\Catalog\Demo\DemoTableConstructor;
use App\Core\Components\Catalog\Dto\Attribute;
use App\Core\Components\Catalog\Dto\Body;
use App\Core\Components\Catalog\Dto\Cell;
use App\Core\Components\Catalog\Dto\Collections\Attributes;
use App\Core\Components\Catalog\Dto\Collections\Cells;
use App\Core\Components\Catalog\Dto\Collections\Rows;
use App\Core\Components\Catalog\Dto\Row;
use App\Core\Components\Catalog\Dto\Table;
use App\Core\Contracts\EntityManagerServiceInterface;
use App\Core\Contracts\RequestValidatorFactoryInterface;
use App\Core\ResponseFormatter;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;


class EntityCatalogController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly ResponseFormatter $responseFormatter,
        private readonly EntityManagerServiceInterface $entityManagerService,
        private readonly FormFactoryInterface $formFactory,
//        private readonly RequestService $requestService,
    )
    {
    }


    function filterData($filters)
    {
        // Здесь должна быть логика фильтрации данных в соответствии с переданными параметрами
        // В этом примере просто возвращается статический набор данных
        $data = [
            ['id' => 1, 'name' => 'Item 1', 'description' => 'Description 1'],
            ['id' => 2, 'name' => 'Item 2', 'description' => 'Description 2'],
            ['id' => 3, 'name' => 'Item 3', 'description' => 'Description 3'],
            // Добавьте другие данные по мере необходимости
        ];

        // Пример простой фильтрации данных
        $filteredData = array_filter($data, static function ($item) use ($filters) {
            $valid = true;
            foreach ($filters as $key => $value) {
                if (!empty($value) && isset($item[$key]) && $item[$key] !== $value) {
                    $valid = false;
                    break;
                }
            }
            return $valid;
        });

        return array_values($filteredData);
    }

    public function index(Response $response): Response
    {
        $t = new DemoTableConstructor();
        $tableContent = $t->shortExample();


        return $this->twig->render($response, 'catalog/index.twig', ['tableContent' => $tableContent]);
    }

    public function filter(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        // Здесь вы должны обработать запрос, фильтровать данные и вернуть результат
        // В данном примере предполагается, что вы будете использовать статичные данные
        $filteredData = $this->filterData($data); // Функция, которая фильтрует данные
        return $this->responseFormatter->asJson($response, $filteredData);
    }

    public function create(Request $request, Response $response): Response
    {
        $form = $this->formFactory->createBuilder()->add('name', TextType::class)->getForm();

//        $form = $this->formFactory->create(RegisterUserForm::class, null, [
//            'user_provider' => $this->userProviderService
//        ]);
//        $form->handleRequest();

//        $data = $this->requestValidatorFactory->make(CreateCategoryRequestValidator::class)->validate(
//            $request->getParsedBody()
//        );
//
//        $category = $this->categoryService->create($data['name'], $request->getAttribute('user'));
//
//        $this->entityManagerService->sync($category);
        return $this->twig->render($response, 'catalog_edit_form.twig', [
            'form' => $form->createView(),
        ]);
    }

//    public function get(Response $response, Category $category): Response
//    {
//        $data = ['id' => $category->getId(), 'name' => $category->getName()];
//
//        return $this->responseFormatter->asJson($response, $data);
//    }
//
//    public function delete(Response $response, Category $category): Response
//    {
//        $this->entityManagerService->delete($category, true);
//
//        return $response;
//    }
//
//    public function update(Request $request, Response $response, Category $category): Response
//    {
//        $data = $this->requestValidatorFactory->make(UpdateCategoryRequestValidator::class)->validate(
//            $request->getParsedBody()
//        );
//
//        $this->entityManagerService->sync($this->categoryService->update($category, $data['name']));
//
//        return $response;
//    }
}
