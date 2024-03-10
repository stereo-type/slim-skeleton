<?php
/**
 * @package  AbstractDataProvider.php
 * @copyright 03.03.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Providers;

use DateTime;
use ReflectionClass;
use InvalidArgumentException;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\QueryException;
use Doctrine\Common\Collections\Expr\Comparison;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Core\Components\Catalog\Enum\FilterType;
use App\Core\Components\Catalog\Enum\EntityButton;
use App\Core\Components\Catalog\Model\Filter\TableQueryParams;
use App\Core\Components\Catalog\Model\Filter\Type\Filter;
use App\Core\Components\Catalog\Model\Filter\Collections\Filters;
use App\Core\Components\Catalog\Model\Filter\Collections\FilterComparisons;
abstract class EntityDataProvider extends AbstractDataProvider implements CatalogFormInterface
{

    public const ENTITY_CLASS = null;

    public const ENTITY_ALIES = 'e';

    public const DATE_FORMAT = 'd.m.Y';

    protected ReflectionClass $reflection;

    public function __construct(
        EntityManager $entityManager,
        private readonly FormFactoryInterface $formFactory,
        ?TableQueryParams $params = null
    ) {
        if (is_null(static::ENTITY_CLASS)) {
            throw new InvalidArgumentException('Должен быть определен класс сущности');
        }
        if (!class_exists((string)static::ENTITY_CLASS)) {
            throw new InvalidArgumentException('Класс сущности не существует ' . static::ENTITY_CLASS);
        }

        $this->reflection = new ReflectionClass((string)static::ENTITY_CLASS);
        $entityAttributes = $this->reflection->getAttributes('Doctrine\ORM\Mapping\Entity');
        if (empty($entityAttributes)) {
            throw new InvalidArgumentException('Класс сущности должен иметь аттрибут Doctrine\ORM\Mapping\Entity');
        }

        parent::__construct($entityManager, $params ?? new TableQueryParams(orderBy: self::ENTITY_ALIES . '.id'));
    }

    /**Метод исключения свойств сущности
     * @return array
     */
    public function exclude_entity_properties(): array
    {
        return [];
    }

    /**Метод ппереименования свойств сущности
     * @return array
     */
    public function named_properties(): array
    {
        return [];
    }

    /**Метод исключения свойств сущности из фильтров
     * @return array
     */
    public function exclude_entity_filters(): array
    {
        return [];
    }


    public function get_properties(bool $all = false): array
    {
        $head = array_map(static function ($item) {
            return $item->name;
        }, $this->reflection->getProperties());
        $head = array_combine($head, $head);

        /**Подставновка переопределений имен*/
        $names = $this->named_properties();
        foreach ($head as $k => $v) {
            if (array_key_exists($k, $names)) {
                $head[$k] = $names[$k];
            }
        }

        if (!$all) {
            /**Исключение свойств сущности*/
            foreach ($this->exclude_entity_properties() as $e) {
                if (array_key_exists($e, $head)) {
                    unset($head[$e]);
                }
            }
        }
        return $head;
    }


    public function head(): array
    {
        $head = $this->get_properties();
        $head[] = 'Управление';
        return $head;
    }


    /**
     * @param TableQueryParams $params
     * @return QueryBuilder
     * @throws QueryException
     */
    public function get_query(TableQueryParams $params): QueryBuilder
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select(self::ENTITY_ALIES)
            ->from(static::ENTITY_CLASS, self::ENTITY_ALIES);

        $props = array_keys($this->get_properties());

        $allowed = FilterComparisons::fromArray(
            array_combine($props, array_fill(0, count($props), Comparison::CONTAINS))
        );

        return $params->filters->fill_query_builder($qb, self::ENTITY_ALIES, $allowed);
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function filters(array $filterData): Filters
    {
        $filters = [];
        $exclude_filters = $this->exclude_entity_filters();
        foreach ($this->get_properties() as $key => $prop) {
            if (!in_array($key, $exclude_filters)) {
                /**TODO сделать поддержку других фильтров*/
                $filters[] = Filter::create(FilterType::input, $prop, ['placeholder' => $prop]);
            }
        }

        return new Filters($filters);
    }


    /**
     * @param Twig $twig
     * @param array $item
     * @return array
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function transform_data_row(Twig $twig, array $item): array
    {
        $result = [];
        $exclude = $this->exclude_entity_properties();
        foreach ($item as $k => $v) {
            if (in_array($k, $exclude)) {
                continue;
            }
            if (is_null($v)) {
                $result[] = $v;
            } elseif (is_string($v)) {
                $result[] = $v;
            } elseif (is_numeric($v)) {
                $result[] = $v;
            } elseif (is_bool($v)) {
                $result[] = (int)$v;
            } elseif ($v instanceof DateTime) {
                $result[] = date(static::DATE_FORMAT, $v->getTimestamp());
            } else {
                throw new InvalidArgumentException('Unsupported type ' . gettype($v));
            }
        }
        $result[] = $this->manage_buttons($twig, (int)$item['id']);
        return $result;
    }

    /**
     * @return EntityButton[]
     */
    protected function buttons(): array
    {
        return [EntityButton::copy, EntityButton::edit, EntityButton::delete];
    }

    /**
     * @param Twig $twig
     * @param int $id
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function manage_buttons(Twig $twig, int $id): string
    {
        $buttons = array_map(static fn($item) => $item->toMap(), $this->buttons());
        return $twig->fetch('/catalog/manage_buttons.twig', ['buttons' => $buttons]);
    }



    public function form(): FormInterface
    {
        $formBuilder = $this->formFactory->createNamedBuilder(
            'catalog_entity_builder',
            FormType::class,
            null,
            ['data_class' => static::ENTITY_CLASS]
        );

        $metadata = $this->entityManager->getClassMetadata(static::ENTITY_CLASS);

        foreach ($metadata->getFieldNames() as $fieldName) {
            if (!$metadata->isIdentifier($fieldName)) {
                $options = [];
                $fieldType = TextType::class;

                if ($metadata->getTypeOfField($fieldName) === 'datetime') {
                    $fieldType = DateType::class;
                    $options = ['data' => new DateTime()];
                }

                $formBuilder->add($fieldName, $fieldType, $options);
            }
        }

        $formBuilder->add('submit', SubmitType::class, ['label' => 'Submit']);
        return $formBuilder->getForm();
    }

}