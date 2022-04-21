<?php

namespace App\Services;

// use App\Exports\DatatableExport;
use Closure;
// use Excel;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
// use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class PaginationService
{
    // private const EXPORT_METHOD = [
    //     'all' => 'all',
    //     'filtered' => 'filtered',
    // ];

    private const LIMIT = 15;

    private const ORDER = 'asc';

    private const ORDER_BY = 'created_at';

    private bool $fullTextSearch = false;

    private EloquentBuilder|QueryBuilder|Relation $query;

    private array $config;

    private string $resource;

    private Collection $data;

    public function __construct(EloquentBuilder|QueryBuilder|Relation $query, string $resource, array $config = [])
    {
        $this->query = $query;
        $this->resource = $resource;
        $this->config = $config;
    }

    public function process(): Response|array
    {
        // Export or paginate.
        // return request()->has('export') ? $this->export() : $this->paginate();
        return $this->paginate();
    }

    private function paginate(): array
    {
        // Search
        $this->search()
            ->filterByColumn()
            ->filter()
            ->sort()
            ->range()
            ->limit()
            ->get();

        return [
            'data' => $this->resource::collection($this->data),
            'meta' => $this->meta()
        ];
    }

    // private function export(): BinaryFileResponse
    // {
    //     if (self::EXPORT_METHOD['filtered'] === request()->query('export')) {
    //         $this->search()
    //             ->filterByColumn()
    //             ->filter()
    //             ->sort()
    //             ->range();
    //     }

    //     $this->get();

    //     return Excel::download(new DatatableExport(
    //         $this->data,
    //         $this->config['export']['headings']
    //             ?? $this->data->count() ? array_keys($this->data[0]->toArray()) : [],
    //         $this->config['export']['headings'] ?? function ($data) {
    //             return array_values($data->toArray());
    //         },
    //     ), $this->getExportFilename());
    // }

    public function meta(): array
    {
        return [
            'total' => $this->data->count(),
            'per_page' => $this->getPageLimit(),
            'current_page' => $this->getPage(),
            'last_page' => $this->getPages(),
            'from' => $this->getOffsetStart() + ($this->data->count() ? 1 : 0),
            'to' => $this->getOffsetStart() + $this->data->count(),
            'order' => $this->getOrder(),
            'order_by' => $this->getOrderBy(),
        ];
    }

    private function search(): PaginationService
    {
        $searchQuery = $this->getSearchQuery();
        if (! is_string($searchQuery)) {
            return $this;
        }

        $searchColumns = $this->getSearchColumns();
        if (is_callable($searchColumns)) {
            $searchColumns($this->query, $searchQuery);
            return $this;
        }

        foreach ($searchColumns as $index => $column) {
            $this->addSearchIndex($column, $index === 0);
        }
        return $this;
    }

    public function addSearchIndex(string $column, bool $isFirstIndex = false): PaginationService
    {
        $searchQuery = $this->getSearchQuery();
        $whereMethod = $isFirstIndex ? 'where' : 'orWhere';
        $this->query->$whereMethod(
            $column,
            $this->fullTextSearch ? '=' : 'like',
            $this->fullTextSearch ? $searchQuery : "%{$searchQuery}%",
        );
        return $this;
    }

    private function filterByColumn(): PaginationService
    {
        foreach ($this->getFilterColumns() as $column) {
            if (request()->has($column)) {
                $this->query->where($column, request()->query($column));
            }
        }
        return $this;
    }

    private function filter(): PaginationService
    {
        if ($filter = $this->getFilter()) {
            $filter($this->query);
        }
        return $this;
    }

    private function sort(): PaginationService
    {
        $this->query->reorder($this->getOrderBy(), $this->getOrder());
        return $this;
    }

    private function range(): PaginationService
    {
        if (! $rangeColumn = $this->getRangeColumn()) {
            return $this;
        }

        if ($rangeStart = $this->getRangeStart()) {
            $this->query->where($rangeColumn, '>=', $rangeStart);
        }

        if ($rangeEnd = $this->getRangeEnd()) {
            $this->query->where($rangeColumn, '<=', $rangeEnd);
        }

        return $this;
    }

    private function limit(): PaginationService
    {
        $this->query
            ->offset($this->getOffsetStart())
            ->limit($this->getOffsetEnd());
        return $this;
    }

    public function get(): PaginationService
    {
        $this->data = $this->query->get();
        return $this;
    }

    public function getSearchQuery(): ?string
    {
        return request()->query('search');
    }

    public function getSearchColumns(): array
    {
        return $this->config['search_columns'] ?? [];
    }

    public function getFilter(): ?Closure
    {
        $filter = request()->query('filter');
        return $filter ? $this->getFilterByName($filter) : null;
    }

    public function getFilterByName(string $filterName): ?Closure
    {
        $filters = $this->config['filters'] ?? [];
        return in_array($filterName, $filters) ? $filters[$filterName] : null;
    }

    public function getFilterColumns(): array
    {
        return $this->config['filter_columns'] ?? [];
    }

    public function getOrder(): string
    {
        $order = request()->query('order');
        return in_array($order, ['asc', 'desc']) ? $order : self::ORDER;
    }

    public function getOrderBy(): string
    {
        return request()->query('order_by') ?? self::ORDER_BY;
    }

    public function getRangeColumn(): ?string
    {
        return request()->query('range');
    }

    public function getRangeEnd(): ?string
    {
        return request()->query('range_end');
    }

    public function getRangeStart(): ?string
    {
        return request()->query('range_start');
    }

    public function getOffsetEnd(): int
    {
        return $this->getPage() * $this->getPageLimit();
    }

    public function getOffsetStart(): int
    {
        return ($this->getPage() - 1) * $this->getPageLimit();
    }
    public function getPageLimit(): int
    {
        $pageLimit = request()->query('limit');
        return (int) is_string($pageLimit) ? $pageLimit : self::LIMIT;
    }

    public function getPage(): int
    {
        $page = request()->query('page');
        return (int) is_string($page) ? $page : 1;
    }

    public function getPages(): float
    {
        return ceil($this->data->count() / $this->getPageLimit());
    }

    public function getExportFilename(): string
    {
        return 'export-' . now() . '.xlsx';
    }
}
