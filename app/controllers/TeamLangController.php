<?php 

namespace app\controllers;

use app\core\Request;
use app\core\Response;
use app\models\TeamLang;
use Exception;

class TeamLangController {
    public function update(Request $request, int $id)
    {
        $teamLang = TeamLang::find($id); 
    
        if (!$teamLang) {
            return Response::json([
                'success' => false,
                'message' => '언어 데이터를 찾을 수 없습니다.'
            ]);
        }

        $teamLang->name = $request->input('name'); 
        $success = $teamLang->save();   
    
        if ($success) {
            return Response::json([
                'success'=> true, 
                'message' => '저장되었습니다.'
            ]);
        }

        return Response::json([
            'success'=> false, 
            'message' => '저장에 실패했습니다.'
        ]);
    }
}
