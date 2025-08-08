<?php 

namespace app\controllers;

use app\core\Request;
use app\core\Response;
use app\core\UploadedFile;
use Exception;

class UploadController
{
    public function summernote(Request $request)
    {
        try {
            $file = $request->file('file');

            if (!$file instanceof UploadedFile || !$file->hasUploaded()) {
                throw new Exception('파일이 업로드되지 않았습니다.');
            }

            if (!$file->isValid() || !$file->isAllowedMimeType('image')) {
                throw new Exception('유효하지 않은 이미지 파일입니다.');
            }


            $path = $file->move(UPLOAD_PATH . DS . 'summernote');

            return Response::json([
                'success' => true,
                'path' => UPLOAD_URL . '/' . $path,
            ]);
        } catch (Exception $e) {
            return Response::json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
