<?php 

namespace app\controllers;

use app\core\Request;
use app\core\Response;
use app\core\UploadedFile;
use Exception;

class UploadController
{
    /**
     * /admin/uploads/summernote?type=image|video
     */
    public function summernote(Request $request)
    {
        try {
            $type = strtolower((string)$request->get('type', 'image'));
            if (!in_array($type, ['image', 'video'], true)) {
                throw new Exception('허용되지 않은 업로드 타입입니다. (image|video)');
            }

            $file = $request->file('file');

            dd($file); 
            if (!$file instanceof UploadedFile || !$file->hasUploaded()) {
                throw new Exception('파일이 업로드되지 않았습니다.');
            }
            if (!$file->isValid()) {
                // 상세 원인 노출
                throw new Exception('손상되었거나 유효하지 않은 업로드입니다. ('.$file->getErrorMessage().')');
            }
            switch ($type) {
                case 'image':
                    return $this->uploadImage($file);
                case 'video':
                    return $this->uploadVideo($file);
            }

            throw new Exception('처리할 수 없는 타입입니다.');
        } catch (Exception $e) {
            return Response::json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * 이미지 업로드 처리
     */
    private function uploadImage(UploadedFile $file)
    {
        if (!$file->isAllowedMimeType('image')) {
            throw new Exception('유효하지 않은 이미지 파일입니다.');
        }

        // 저장 경로: /summernote/images
        $savedRelativePath = $file->move(UPLOAD_PATH . DS . 'summernote' . DS . 'images');

        // 퍼블릭 URL
        $publicUrl = rtrim(UPLOAD_URL, '/') . '/' . ltrim($savedRelativePath, '/');

        return Response::json([
            'success' => true,
            'type'    => 'image',
            'path'    => $publicUrl,
        ]);
    }

    /**
     * 비디오 업로드 처리
     */
    private function uploadVideo(UploadedFile $file)
    {
        if (!$file->isAllowedMimeType('video')) {
            throw new Exception('유효하지 않은 비디오 파일입니다.');
        }

        // 저장 경로: /summernote/videos
        $savedRelativePath = $file->move(UPLOAD_PATH . DS . 'summernote' . DS . 'videos');

        // 퍼블릭 URL
        $publicUrl = rtrim(UPLOAD_URL, '/') . '/' . ltrim($savedRelativePath, '/');

        return Response::json([
            'success' => true,
            'type'    => 'video',
            'path'    => $publicUrl,
        ]);
    }
}
