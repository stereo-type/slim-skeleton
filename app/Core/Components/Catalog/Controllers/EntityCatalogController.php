<?php

declare(strict_types = 1);

namespace App\Core\Components\Catalog\Controllers;

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
    ) {
    }

    public function index(Response $response): Response
    {
        return $this->twig->render($response, 'categories/index.twig');
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
        return  $this->twig->render($response, 'catalog_edit_form.twig', [
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
