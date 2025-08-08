<?php

namespace app\controllers;

use app\core\Request;
use app\core\Response;
use app\models\SettingLang;

class SettingLangController
{
    public function update(Request $request, int $id)
    {
        $lang = SettingLang::find($id);

        if (!$lang) {
            return Response::json([
                'success' => false,
                'message' => '언어 데이터를 찾을 수 없습니다.'
            ]);
        }

        $data = $request->only([
            'tel', 'fax', 'address', 'youtube_link',
            'site_name', 'meta_title', 'meta_description', 'meta_image'
        ]);

        foreach ($data as $key => $value) {
            $lang->$key = $value;
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
