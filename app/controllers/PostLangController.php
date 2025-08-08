<?php

namespace app\controllers;

use app\core\Request;
use app\core\Response;
use app\core\UploadedFile;
use app\models\PostLang;

class PostLangController
{
    public function update(Request $request, int $id)
    {
        $lang = PostLang::find($id);

        if (!$lang) {
            return Response::json([
                'success' => false,
                'message' => '언어 데이터를 찾을 수 없습니다.'
            ]);
        }

        $data = $request->only([
            'extra1', 'extra2', 'extra3', 'extra4', 'extra5',
            'extra6', 'extra7', 'extra8', 'extra9', 'extra10',
        ]);

        foreach ($data as $key => $value) {
            $lang->$key = $value;
        }

        for ($i = 1; $i <= 10; $i++) {
            $key = "image{$i}";
            $file = $request->file($key);
            if ($file instanceof UploadedFile && $file->hasUploaded()) {
                if (!$file->isValid() || !$file->isAllowedMimeType('image')) {
                    continue;
                }
                $lang->$key = $file->move(UPLOAD_PATH . DS . 'posts');
            }

            if ($request->input("delete_{$key}")) {
                UploadedFile::delete($lang->$key);
                $lang->$key = '';
            }
        }

        if (!$lang->save()) {
            return Response::json([
                'success' => false,
                'message' => '저장에 실패했습니다.'
            ]);
        }

        return Response::json([
            'success' => true,
            'message' => '저장되었습니다.',
            'data' => $lang->getAttributes()
        ]);
    }
}
