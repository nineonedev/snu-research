<?php
// callback 함수명 그대로 전달
$callback = isset($_REQUEST['callback_func']) ? $_REQUEST['callback_func'] : '';
$url = 'callback.html?callback_func=' . rawurlencode($callback);

// 업로드 성공 여부
$bSuccessUpload = isset($_FILES['Filedata']['tmp_name'])
    && is_uploaded_file($_FILES['Filedata']['tmp_name']);

if ($bSuccessUpload) {
    $tmp  = $_FILES['Filedata']['tmp_name'];
    $name = $_FILES['Filedata']['name'];

    // 확장자/ MIME 체크
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    $allow_image = ['jpg','jpeg','png','gif','bmp','webp'];
    $allow_video = ['mp4','webm','ogg','mov','avi','wmv','3gp','mkv'];
    $allow_all   = array_merge($allow_image, $allow_video);

    if (!in_array($ext, $allow_all, true)) {
        header('Location: '.$url.'&errstr=invalid_ext'); exit;
    }

    // MIME 확인(보안)
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $tmp);
    finfo_close($finfo);

    $isImage = (strpos($mime, 'image/') === 0);
    $isVideo = (strpos($mime, 'video/') === 0) || in_array($ext, $allow_video, true);

    if (!$isImage && !$isVideo) {
        header('Location: '.$url.'&errstr=invalid_mime'); exit;
    }

    // (선택) 동영상 크기 제한 예시: 200MB
    if ($isVideo && filesize($tmp) > 200 * 1024 * 1024) {
        header('Location: '.$url.'&errstr=too_large'); exit;
    }

    // 저장 경로
    $uploadDir = rtrim($_SERVER['DOCUMENT_ROOT'], '/').'/uploads/smarteditor/';
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
        header('Location: '.$url.'&errstr=mkdir_failed'); exit;
    }

    // 충돌 없는 파일명
    $basename = bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;
    $destPath = $uploadDir . $basename;

    if (!move_uploaded_file($tmp, $destPath)) {
        header('Location: '.$url.'&errstr=move_failed'); exit;
    }

    // URL은 인코딩
    $encoded  = rawurlencode($basename);
    $fileUrl  = '/uploads/smarteditor/' . $encoded;
    $sType    = $isVideo ? 'video' : 'image';

    $query  = '&bNewLine=true';
    $query .= '&sType='      . $sType;           // ← 타입 전달(영상/이미지 구분용)
    $query .= '&sFileName='  . $encoded;
    $query .= '&sFileURL='   . $fileUrl;
    $query .= '&sUploadFile='. $encoded;

    header('Location: '.$url.$query);
    exit;

} else {
    header('Location: '.$url.'&errstr=error');
    exit;
}
