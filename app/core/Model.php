<?php

namespace app\core; 

use app\core\Paginator;
use app\core\QueryBuilder;

abstract class Model
{
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $attributes = [];
    protected array $original = [];
    protected static array $observers = [];

    public function __construct(array $data = [])
    {
        $this->fill($data);
        $this->original = $this->attributes;
    }

    public function __get($key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public static function first(string $column, string $operator = '=', $value = null): ?self
    {
        $data = static::query()->where($column, $operator, $value)->first();
        
        return $data ? new static($data) : null;
    }


    public static function query(): QueryBuilder
    {
        $instance = new static;
        return new QueryBuilder($instance->table);
    }

    public static function where(string $column, string $operator = '=', $value = null): ?array
    {
        $data = static::query()->where($column, $operator, $value)->get();
    
        if ($data) {
            return array_map(fn($item) => new static($item), $data);
        }

        return null;
    }

    public static function fromRow(array $row): self
    {
        return new static($row);
    }


    public static function paginate(int $perPage = 15, int $page = 1): Paginator
    {
        return static::query()->paginate($perPage, $page);
    }

    public static function exists(string $column, string $operator = '=', $value = null): bool
    {
        return static::query()->where($column, $operator, $value)->exists();
    }


    public static function find($id): ?self
    {
        $instance = new static;
        $data = static::query()->where($instance->primaryKey, '=', $id)->first();
    
        return $data ? new static($data) : null;
    }

    public static function all(): array
    {
        return array_map(fn($row) => new static($row), static::query()->get());
    }

    public static function create(array $data): ?self
    {
        $model = new static($data);
        if ($model->save()) {
            return $model;
        }
        return null;
    }

    public function save(): bool
    {
        if (isset($this->attributes[$this->primaryKey])) {
            return $this->performUpdate();
        }
        return $this->performInsert();
    }

    protected function performInsert(): bool
    {
        $id = static::query()->insert($this->attributes);

        if ($id) {
            $this->attributes[$this->primaryKey] = $id;
            $this->original = $this->attributes;
            return true;
        }
        return false;
    }

    protected function performUpdate(): bool
    {
        $changes = $this->getChanges();

        if (empty($changes)) {
            return true; // nothing to update
        }

        // fillable에 있는 필드만 필터링 (primaryKey는 제외)
        $fillableChanges = [];
        foreach ($changes as $key => $value) {
            if ($key === $this->primaryKey) {
                continue; // primaryKey는 업데이트하지 않음
            }
            if (in_array($key, $this->fillable)) {
                $fillableChanges[$key] = $value;
            }
        }

        // fillable 필드가 없으면 업데이트할 것이 없음
        if (empty($fillableChanges)) {
            $this->original = $this->attributes; // original을 업데이트하여 변경사항 초기화
            return true;
        }

        $result = static::query()
            ->where($this->primaryKey, '=', $this->attributes[$this->primaryKey])
            ->update($fillableChanges);

        // rowCount()는 영향을 받은 행 수를 반환 (0 이상의 정수)
        // SQL 에러가 발생하면 예외가 던져지므로, 여기까지 왔다면 성공
        // 같은 값으로 업데이트해도 rowCount()가 0일 수 있지만, 이는 성공으로 간주
        if ($result !== false) {
            $this->original = $this->attributes;
            return true;
        }

        return false;
    }

    public function delete(): bool
    {
        $result = static::query()
            ->where($this->primaryKey, '=', $this->attributes[$this->primaryKey])
            ->delete();
        return $result;
    }

    public function fill(array $data): void
    {
        foreach ($this->fillable as $key) {
            if (array_key_exists($key, $data)) {
                $this->attributes[$key] = $data[$key];
            }
        }
    }

    public function isDirty(): bool
    {
        return $this->getChanges() !== [];
    }

    public function getChanges(): array
    {
        $changes = [];
        foreach ($this->attributes as $key => $value) {
            if (!array_key_exists($key, $this->original)) {
                $changes[$key] = $value;
            } else {
                // 타입 변환을 고려한 비교 (int와 string 비교 등)
                $originalValue = $this->original[$key];
                // 숫자 문자열과 숫자를 비교
                if (is_numeric($value) && is_numeric($originalValue)) {
                    if ((float)$value !== (float)$originalValue) {
                        $changes[$key] = $value;
                    }
                } elseif ($value !== $originalValue) {
                    $changes[$key] = $value;
                }
            }
        }
        return $changes;
    }

    public function wasChanged(): bool
    {
        return $this->isDirty();
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

}
