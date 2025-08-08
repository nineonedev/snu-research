<?php

namespace app\controllers;

use app\core\Request;
use app\core\Response;
use app\models\BannerLang;

class BannerLangController
{
    public function update(Request $request, int $id)
    {
        $lang = BannerLang::find($id);

        if (!$lang) {
            return Response::json([
                'success' => false,
                'message' => '언어 데이터를 찾을 수 없습니다.'
            ]);
        }

        $lang->title = $request->input('title');
        $lang->description = $request->input('description');
        $lang->link = $request->input('link');

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
