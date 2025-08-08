<?php 
    extend('admin'); 
    section('content');
?>

<section class="no-section-sm">
    <div class="no-container-md">
        <div class="no-admin-content-top">
            <h1 class="no-heading-xs">게시판 생성</h1>
            
        </div>
    </div>
</section>

<section class="no-section-sm--b">
    <div class="no-container-md">
        <div class="no-admin-box">
            <div>
                <h2 class="no-heading-xxs">검색</h2>
            </div>

            <div class="no-mg-16--t">
                
                <div class="no-form-control">
                    <label for="title">제목</label>
                    <input type="text" name="title" id="title">
                </div>

                <div class="no-form-control">
                    <label for="file">파일</label>
                    <input type="file" name="file" id="file">
                </div>

                <div class="no-form-control">
                    <label for="content">내용</label>
                    <textarea name="content" id="content"></textarea>
                </div>

                <div class="no-form-control">
                    <label for="summernote">내용</label>
                    <textarea name="summernote" id="summernote"></textarea>
                </div>

                <div class="no-row">
                    <div class="no-col-6 no-col-md-12">
                        <div class="no-form-label">공지여부</div>
                        <div class="no-form-list">
                            <div class="no-form-check">
                                <label for="is_notice">
                                    <input type="checkbox" name="is_notice" id="is_notice">
                                    <div class="no-form-check-box">
                                        <i class="fa-regular fa-check"></i>
                                    </div>
                                    <span class="no-form-check-text">공지로 설정합니다.</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="no-col-6 no-col-md-12">
                        <div class="no-form-label">활성여부</div>
                        <div class="no-form-list">
                            <div class="no-form-switch">
                                <label for="is_running">
                                    <input type="checkbox" name="is_running" id="is_running">
                                    <div class="no-form-switch-box">
                                        <span class="no-form-switch-knob"></span>
                                    </div>
                                    <span class="no-form-switch-text">공지로 설정합니다.</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="no-col-6 no-col-md-12">
                        <span class="no-form-label">성별</span>
                        <div class="no-form-list">
                            <div class="no-form-radio">
                                <label for="is_radio_1">
                                    <input type="radio" name="is_radio" id="is_radio_1" checked>
                                    <div class="no-form-radio-box">
                                        <span class="no-form-radio-circle"></span>
                                    </div>
                                    <span class="no-form-radio__text">남</span>
                                </label>
                            </div>
                            <div class="no-form-radio">
                                <label for="is_radio_2">
                                    <input type="radio" name="is_radio" id="is_radio_2">
                                    <div class="no-form-radio-box">
                                        <span class="no-form-radio-circle"></span>
                                    </div>
                                    <span class="no-form-radio__text">여</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- group -->

            </div>

        </div>
    </div>
</section>

<div class="no-section-lg--b">
    <div class="no-container-md">
        <menu class="no-admin-action">
            <a href="./" class="no-btn-white">
                <span>목록</span>
            </a>
            <button class="no-btn-primary">
                <span>수정</span>
            </button>
            <button class="no-btn-error">
                <span>삭제</span>
            </button>
        </menu>
    </div>
</div>


<?php endSection() ?>
