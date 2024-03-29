<?php
//
//declare(strict_types=1);
//
//namespace App\Features\Category\Service;
//
//
//use Doctrine\ORM\Tools\Pagination\Paginator;
//
//use App\Core\Entity\User;
//use App\Core\Contracts\EntityManagerServiceInterface;
//use App\Core\DataObjects\DataTableQueryParams;
//use App\Features\Category\Entity\Category;
//
//readonly class CategoryService
//{
//    public function __construct(private EntityManagerServiceInterface $entityManager)
//    {
//    }
//
//    public function create(string $name, User $user): Category
//    {
//        $category = new Category();
//
//        $category->setUser($user);
//
//        return $this->update($category, $name);
//    }
//
//    public function getPaginatedCategories(DataTableQueryParams $params): Paginator
//    {
//        $query = $this->entityManager
//            ->getRepository(Category::class)
//            ->createQueryBuilder('c')
//            ->setFirstResult($params->start)
//            ->setMaxResults($params->length);
//
//        $orderBy = in_array($params->orderBy, ['name', 'createdAt', 'updatedAt']) ? $params->orderBy : 'updatedAt';
//        $orderDir = strtolower($params->orderDir) === 'asc' ? 'asc' : 'desc';
//
//        if (!empty($params->searchTerm)) {
//            $query->where('c.name LIKE :name')->setParameter(
//                'name',
//                '%'.addcslashes($params->searchTerm, '%_').'%'
//            );
//        }
//
//        $query->orderBy('c.'.$orderBy, $orderDir);
//
//        return new Paginator($query);
//    }
//
//    public function getById(int $id): ?Category
//    {
//        return $this->entityManager->find(Category::class, $id);
//    }
//
//    public function update(Category $category, string $name): Category
//    {
//        $category->setName($name);
//
//        return $category;
//    }
//
//    public function getCategoryNames(): array
//    {
//        return $this->entityManager
//            ->getRepository(Category::class)->createQueryBuilder('c')
//            ->select('c.id', 'c.name')
//            ->getQuery()
//            ->getArrayResult();
//    }
//
//    public function findByName(string $name): ?Category
//    {
//        return $this->entityManager->getRepository(Category::class)->findBy(['name' => $name])[0] ?? null;
//    }
//
//    public function getAllKeyedByName(): array
//    {
//        $categories = $this->entityManager->getRepository(Category::class)->findAll();
//        $categoryMap = [];
//
//        foreach ($categories as $category) {
//            $categoryMap[strtolower($category->getName())] = $category;
//        }
//
//        return $categoryMap;
//    }
//}
