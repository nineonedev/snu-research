<?php 
    extend('admin'); 
    section('content');
?>

<section class="no-section-sm">
    <div class="no-container-xl">
        <div class="no-admin-content-top">
            <h1 class="no-heading-xs">게시판 관리</h1>
            <div>
                <a href="/admin/posts/create" class="no-btn-primary">
                    <span>Create a new</span>
                </a>
            </div>
        </div>
    </div>
</section>
<section>
    <div class="no-container-xl">
        <div class="no-admin-box">
            <h2 class="no-heading-xxs">검색</h2>
            <div class="no-row no-mg-16--t">
                <div class="no-col-4">
                    <div class="no-form-control">
                        <label for="title">제목</label>
                        <input type="text" name="title" id="title">
                    </div>
                </div>
                <div class="no-col-4">
                    <div class="no-form-control">
                        <label for="title">제목</label>
                        <input type="text" name="title" id="title">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="no-section-xs--t">
    <div class="no-container-xl">
        <div class="no-admin-box">
            <h2 class="no-heading-xxs">게시글 목록</h2>
            <div class="no-mg-16--t">
                <table class="no-table">
                    <thead>
                        <tr>
                            <th scope="col">번호</th>
                            <th scope="col">이미지</th>
                            <th scope="col">제목</th>
                            <th scope="col">생성일</th>
                            <th scope="col">관리</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>2</td>
                            <td>LG전자 ESG 대학생 아카데미</td>
                            <td>
                                asdasdas
                            </td>
                            <td>
                                2025-01-20 15:41:23
                            </td>
                            <td>
                                <menu class="no-admin-action">
                                    <a href="./edit.php?id=7" class="no-btn-white">
                                        <span>수정</span>
                                    </a>
                                    <button type="button" class="no-btn-error" data-id="7">
                                        <span>삭제</span>
                                    </button>
                                </menu>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="no-mg-16--t">
                <nav class="no-pagination">
                    <a href="/admin/pages/posts/?page=2" class="no-pagination__arrow">
                        <i class="fa-light fa-chevrons-left"></i>    
                    </a>
                    <a href="/admin/pages/posts/?page=4" class="no-pagination__arrow">
                        <i class="fa-light fa-chevron-left"></i>
                    </a>
                    <div class="no-pagination__num">
                        <a href="/admin/pages/posts/?page=1" class="no-pagination__link active">1</a>
                        <a href="/admin/pages/posts/?page=2" class="no-pagination__link ">2</a>
                        <a href="/admin/pages/posts/?page=3" class="no-pagination__link ">3</a>
                        <a href="/admin/pages/posts/?page=4" class="no-pagination__link ">4</a>
                    </div>
                    <a href="/admin/pages/posts/?page=2" class="no-pagination__arrow">
                        <i class="fa-light fa-chevrons-right"></i>    
                    </a>
                    <a href="/admin/pages/posts/?page=4" class="no-pagination__arrow">
                        <i class="fa-light fa-chevron-right"></i>
                    </a>
                </nav>
            </div>
        </div>
    </div>
</section>


<?php endSection() ?>