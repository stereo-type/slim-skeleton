<?php
/**
 * @package  DataTableQueryParams.php
 * @copyright 23.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Model\Filter;


use App\Core\Components\Catalog\Enum\OrderDir;
use App\Core\Components\Catalog\Model\Filter\Collections\Filters;
use App\Core\Components\Catalog\Model\Filter\Type\Filter;

class TableQueryParams
{
    /**
     * @param Filters $filters
     * @param int $page
     * @param int $perpage
     * @param string $orderBy
     * @param OrderDir $orderDir
     */
    public function __construct(
        public Filters $filters = new Filters(),
        readonly public int $page = 1,
        readonly public int $perpage = 10,
        readonly public string $orderBy = 'id',
        readonly public OrderDir $orderDir = OrderDir::asc,
    ) {
    }

    public static function fromArray(array $data): TableQueryParams
    {
        return new self(
            $data['filters'] ?? new Filters(),
            (int)($data['page'] ?? 1),
            (int)($data['perpage'] ?? 10),
            $data['orderBy'] ?? 'id',
            $data['orderDir'] instanceof OrderDir ? $data['orderDir']
                : (OrderDir::tryFrom($data['orderDir']) ?? OrderDir::asc)
        );
    }

    public function toArray(): array
    {
        return [
            'filters' => $this->filters,
            'page' => $this->page,
            'perpage' => $this->perpage,
            'orderBy' => $this->orderBy,
            'orderDir' => $this->orderDir,
        ];
    }

    public function addFilter(Filter $filter): void
    {
        $this->filters->add($filter);
    }

    public function copyWith(
        ?Filters $filters = null,
        ?int $page = null,
        ?int $perpage = null,
        ?string $orderBy = null,
        ?OrderDir $orderDir = null,
    ): self {
        return new self(
            $filters ?? $this->filters,
            $page ?? $this->page,
            $perpage ?? $this->perpage,
            $orderBy ?? $this->orderBy,
            $orderDir ?? $this->orderDir,
        );
    }


}