<?php 

namespace app\core;

use app\facades\App;

class CursorPaginator {
    public function __construct(
        public array $items,
        public int $perPage,
        public ?string $prevCursor,
        public ?string $nextCursor,
        public string $cursorColumn
    ) {}

    public function hasNext(): bool
    {
        return $this->nextCursor !== null;
    }

    public function nextUrl($baseUrl): ?string
    {
        if (!$this->nextCursor) return null;

        $baseUrl = $baseUrl ?: ($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        $query = App::request()->query();
        $query['cursor'] = $this->nextCursor;

        return $baseUrl . '?' . http_build_query($query);
    }

    public function toArray(): array
    {
        return [
            'per_page' => $this->perPage,
            'prev_cursor' => $this->prevCursor,
            'next_cursor' => $this->nextCursor,
            'has_more' => $this->hasNext(),
            'data' => $this->items,
        ];
    }

    public function links(): string
    {
        $html = '<ul class="pagination">';
    
        if ($this->prevCursor) {
            $html .= '<li><a href="' . $this->buildUrl($this->prevCursor) . '">Prev</a></li>';
        }
    
        if ($this->nextCursor) {
            $html .= '<li><a href="' . $this->buildUrl($this->nextCursor) . '">Next</a></li>';
        }
    
        $html .= '</ul>';
        return $html;
    }
    
    protected function buildUrl(string $cursor): string
    {
        $base = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $query = App::request()->query();
        $query['cursor'] = $cursor;
        return $base . '?' . http_build_query($query);
    }
    
}
