<?php 

namespace app\core;

use app\facades\App;

class Paginator {
    public int $total;
    public int $perPage; 
    public int $currentPage; 
    public int $lastPage; 
    public array $items; 
    public string $basePath; 

    public function __construct(
        array $items = [],
        int $total = 0,
        int $perPage = 15,
        int $currentPage = 1, 
        string $basePath = ''
    )
    {
        $this->items = $items; 
        $this->total = $total;
        $this->perPage = $perPage; 
        $this->currentPage = $currentPage;
        $this->lastPage = $this->totalPages(); 
        $this->basePath = $basePath ?: strtok($_SERVER['REQUEST_URI'], '?');
    }

    function addRowNumbers(array $rows, int $total, int $perPage, int $currentPage): array
    {
        $start = $total - (($currentPage - 1) * $perPage);
        foreach ($rows as $index => &$row) {
            $row['_no'] = $start - $index;
        }
        return $rows;
    }

    public function withNumbers(): self
    {
        $startNo = $this->total - (($this->currentPage - 1) * $this->perPage);
    
        foreach ($this->items as $index => &$item) {
            $item['_no'] = $startNo - $index;
        }
    
        return $this;
    }


    public function links(): string
    {
        $html = '<ul class="pagination">';

        for ($page = 1; $page <= $this->lastPage; $page++) {
            $active = $page === $this->currentPage ? 'class="active"' : '';
            $html .= "<li $active><a href=\"" . $this->pageUrl($page) . "\">{$page}</a></li>";
        }

        $html .= '</ul>';

        return $html;
    }


    public function totalPages(): int
    {
        return (int) ceil($this->total / $this->perPage);
    }

    public function hasMorePages(): bool
    {
        return $this->currentPage < $this->totalPages();
    }

    protected function pageUrl(int $page): string 
    {
        $query = App::request()->query(); 
        $query['page'] = $page; 
        return $this->basePath . '?' . http_build_query($query);
    }

    public function toArray(): array
    {
        $from = ($this->currentPage - 1) * $this->perPage + 1;
        $to = min($from + $this->perPage - 1, $this->total);
        
        return [
            'total'            => $this->total,
            'per_page'         => $this->perPage,
            'current_page'     => $this->currentPage,
            'last_page'        => $this->lastPage,
            'first_page_url'   => $this->pageUrl(1),
            'last_page_url'    => $this->pageUrl($this->lastPage),
            'next_page_url'    => $this->currentPage < $this->lastPage ? $this->pageUrl($this->currentPage + 1) : null,
            'prev_page_url'    => $this->currentPage > 1 ? $this->pageUrl($this->currentPage - 1) : null,
            'path'             => $this->basePath,
            'from'             => $from > $to ? null : $from,
            'to'               => $from > $to ? null : $to,
            'data'             => $this->items,
        ];
    }

    public function render(): string
    {
        $html = '<nav class="no-pagination">';

        // << 처음 페이지
        $html .= '<a href="' . $this->pageUrl(1) . '" class="no-pagination__arrow">
                    <i class="fa-light fa-chevrons-left"></i>
                </a>';

        // < 이전 페이지
        $prev = $this->currentPage > 1 ? $this->currentPage - 1 : 1;
        $html .= '<a href="' . $this->pageUrl($prev) . '" class="no-pagination__arrow">
                    <i class="fa-light fa-chevron-left"></i>
                </a>';

        // 페이지 번호 (현재 페이지부터 최대 5개)
        $start = $this->currentPage;
        $end = min($this->lastPage, $start + 4);

        // 만약 끝 페이지가 5개 이하라면 start를 조정 (최소 1)
        if ($end - $start < 4 && $start > 1) {
            $start = max(1, $end - 4);
        }

        $html .= '<div class="no-pagination__num">';
        for ($page = $start; $page <= $end; $page++) {
            $active = $page === $this->currentPage ? 'active' : '';
            $html .= '<a href="' . $this->pageUrl($page) . '" class="no-pagination__link ' . $active . '">' . $page . '</a>';
        }
        $html .= '</div>';

        // > 다음 페이지
        $next = $this->currentPage < $this->lastPage ? $this->currentPage + 1 : $this->lastPage;
        $html .= '<a href="' . $this->pageUrl($next) . '" class="no-pagination__arrow">
                    <i class="fa-light fa-chevron-right"></i>
                </a>';

        // >> 마지막 페이지
        $html .= '<a href="' . $this->pageUrl($this->lastPage) . '" class="no-pagination__arrow">
                    <i class="fa-light fa-chevrons-right"></i>
                </a>';

        $html .= '</nav>';

        return $html;
    }

}