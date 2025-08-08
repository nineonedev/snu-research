<?php 

namespace app\core;

use app\facades\App;
use app\facades\DB;
use Exception;

class QueryBuilder {
    private string $table; 
    private array $columns = ['*']; 
    private array $where = []; 
    private array $bindings = [];
    private array $orderBy = ['created_at DESC']; 
    private $limit = null; 
    private $offset = null;
    private array $joins = [];
    private array $groupBy = [];
    private array $having = [];

    public static function raw(string $sql, array $bindings = []): array 
    {
        return DB::query($sql, $bindings);
    }
    
    public function __construct(string $table)
    {
        $this->table($table);
    }

    public function whereColumn(string $first, string $operator, string $second): self
    {
        $this->where[] = "$first $operator $second";
        return $this;
    }

    public static function rawExpression(string $sql): string
    {
        return $sql;
    }

    public function table(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function orderByDesc(string $column): self
    {
        $this->orderBy[] = "$column DESC";
        return $this;
    }

    public function pluck(string $column): array
    {
        $this->select([$column]);
        $results = $this->get();

        return array_column($results, $column);
    }

    public function whereExists(QueryBuilder $subQuery): self
    {
        $sql = $subQuery->getSql();
        $this->where[] = "EXISTS ($sql)";
        $this->bindings = array_merge($this->bindings, $subQuery->bindings);
        return $this;
    }

    public function whereNotExists(QueryBuilder $subQuery): self
    {
        $sql = $subQuery->getSql();
        $this->where[] = "NOT EXISTS ($sql)";
        $this->bindings = array_merge($this->bindings, $subQuery->bindings);
        return $this;
    }



    public function select(array $columns = ['*']): self
    {
        $this->columns = $columns;
        return $this; 
    }

    public function first(): ?array
    {
        $this->limit(1);
        $result = $this->get();

        return $result[0] ?? null;
    }

    public function whereNull(string $column): self
    {
        $this->where[] = "$column IS NULL";
        return $this;
    }

    public function whereNotNull(string $column): self
    {
        $this->where[] = "$column IS NOT NULL";
        return $this;
    }

    public function groupBy(string ...$columns): self 
    {
        $this->groupBy = $columns; 
        return $this;    
    }

    public function having(string $column, string $operator, $value): self
    {
        $this->having[] = "$column $operator ?"; 
        $this->bindings[] = $value;
        return $this; 
    }

    public function exists(): bool
    {
        $sql = "SELECT EXISTS(" . $this->getSql() .") as exists_value"; 
        $result = DB::query($sql, $this->bindings); 

        return (bool) ($result[0]['exists_value'] ?? false); 
    }

    public function insert(array $data)
    {
        $columns = implode(', ', array_keys($data)); 
        $placeholders = implode(', ', array_fill(0, count($data), '?')); 
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ($placeholders)"; 

        return DB::query($sql, array_values($data)); 
    }

    public function update(array $data): bool
    {   
        if (empty($this->where)) {
            throw new Exception('Update without WHERE clause is not allowed.');
        }
    
        $setClause = implode(', ', array_map(fn($col) => "$col = ?", array_keys($data)));
        $sql = "UPDATE {$this->table} SET {$setClause} " . $this->compileWhere();
    
        return DB::query($sql, array_merge(array_values($data), $this->bindings));
    }

    public function delete(): bool
    {
        if (empty($this->where)) {
            throw new \RuntimeException('Delete without WHERE clause is not allowed.');
        }
    
        $sql = "DELETE FROM {$this->table} " . $this->compileWhere();
        return DB::execute($sql, $this->bindings);
    }

    public function where(string $column, string $operator, $value): self
    {
        $this->where[] = "$column $operator ?";
        $this->bindings[] = $value;
        return $this; 
    }

    public function whereIn(string $column, array $values): self
    {
        if (empty($values)) {
            $this->where[] = "0 = 1"; 
            return $this;
        }

        $placeholders = implode(', ', array_fill(0, count($values), '?'));
        $this->where[] = "$column IN ($placeholders)";
        $this->bindings = array_merge($this->bindings, $values);

        return $this;
    }


    public function orWhere(string $column, string $operator, $value): self
    {
        $this->where[] = "OR $column $operator ?";
        $this->bindings[] = $value;
        return $this;
    }

    public function andWhere(string $column, string $operator, $value): self
    {
        $this->where[] = "AND $column $operator ?";
        $this->bindings[] = $value;
        return $this;
    }

    public function whereBetween(string $column, array $range): self
    {
        if (count($range) !== 2) {
            throw new \InvalidArgumentException("Range must contain exactly two values.");
        }
    
        $this->where[] = "$column BETWEEN ? AND ?";
        $this->bindings = array_merge($this->bindings, $range);
        return $this;
    }
    

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderBy[] = "$column $direction";
        return $this; 
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this; 
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this; 
    }

    public function count(): int
    {
        $clone = clone $this;
        $clone->columns = ['COUNT(*) as aggregate'];
        $clone->orderBy = [];
        $clone->limit = null;
        $clone->offset = null;

        $sql = $clone->getSql();

        return (int) (DB::query($sql, $clone->bindings)[0]['aggregate'] ?? 0);
    }



    public function join(
        string $table, 
        string $first, 
        string $operator, 
        string $second,
        string $type = 'INNER'
    ): self
    {   
        $this->joins[] = "$type JOIN $table ON $first $operator $second";
        return $this; 
    }

    public function leftJoin(string $table, string $first, string $operator, string $second): self
    {
        return $this->join($table, $first, $operator, $second, 'LEFT');
    }

    public function rightJoin(string $table, string $first, string $operator, string $second): self
    {
        return $this->join($table, $first, $operator, $second, 'RIGHT');
    }


    public function subQuery(QueryBuilder $builder, string $alias): self
    {
        $sql = '(' . $builder->getSql() . ') AS ' . $alias;
        $this->joins[] = "JOIN $sql ON 1 = 1"; 
        return $this;
    }


    public function paginate(int $perPage, int $page): Paginator
    {
        $page = max(1, $page); 

        $offset = ($page - 1) * $perPage;

        $items = (clone $this)->limit($perPage)->offset($offset)->get();
        $total = (clone $this)->count();

        return new Paginator($items, $total, $perPage, $page);
    }

    public function get(): array
    {
        $sql = $this->getSql();
        return DB::query($sql, $this->bindings);
    }

    public function simplePaginate(int $perPage = 15, int $page = 1)
    {
        $offset = ($page - 1) * $perPage; 

        $items = $this->limit($perPage + 1)->offset($offset)->get(); 

        $hasMore = count($items) > $perPage; 

        if($hasMore) {
            array_pop($items); 
        }

        $fakeTotal = $offset + count($items) + ($hasMore ? 1 : 0) ;

        return new Paginator($items, $fakeTotal, $perPage, $page);
    }

    public function cursorPaginate(
        int $perPage = 15, 
        string $column = 'id', 
        ?string $cursor = null
    ): CursorPaginator 
    {
        if ($cursor) {
            $this->where($column, '>', $cursor); 
        }

        $this->orderBy($column, 'ASC'); 
        $items = $this->limit($perPage + 1)->get(); 

        $nextCursor = null; 
        if (count($items) > $perPage) {
            $nextCursor = $items[$perPage][$column];
            array_pop($items); 
        }

        return new CursorPaginator(
            $items, 
            $perPage, 
            $cursor,
            $nextCursor,
            $column
        );
    }

    private function getSql(): string
    {
        return implode(' ', array_filter([
            $this->compileSelect(),
            $this->compileFrom(),
            $this->compileJoins(),
            $this->compileWhere(),
            $this->compileGroupBy(),
            $this->compileHaving(),
            $this->compileOrderBy(),
            $this->compileLimitOffset(),
        ]));
    }

    private function compileSelect(): string
    {
        return "SELECT " . implode(', ', $this->columns);
    }

    private function compileFrom(): string
    {
        return "FROM " . $this->table;
    }

    private function compileJoins(): string
    {
        return !empty($this->joins) ? implode(' ', $this->joins) : '';
    }

    private function compileWhere(): string
    {
        return !empty($this->where) ? 'WHERE ' . implode(' AND ', $this->where) : '';
    }

    private function compileGroupBy(): string 
    {
        return !empty($this->groupBy) 
            ? 'GROUP BY ' . implode(', ', $this->groupBy)
            : '';
    }

    private function compileHaving(): string 
    {
        return !empty($this->having) 
            ? 'HAVING ' . implode(' AND ', $this->having)
            : '';
    }

    private function compileOrderBy(): string
    {
        return !empty($this->orderBy) ? 'ORDER BY ' . implode(', ', $this->orderBy) : '';
    }

    private function compileLimitOffset(): string
    {
        $sql = '';
        if (isset($this->limit)) {
            $sql .= 'LIMIT ' . $this->limit . ' ';
        }
        if (isset($this->offset)) {
            $sql .= 'OFFSET ' . $this->offset;
        }
        return trim($sql);
    }
    
}