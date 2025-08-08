<?php

namespace app\controllers;

use app\core\Request;
use app\core\Response;
use app\models\BoardLang;

class BoardLangController
{
    public function update(Request $request, int $id)
    {
        $lang = BoardLang::find($id);
        if (!$lang) {
            return Response::json(['success' => false]);
        }

        $lang->name = $request->input('name');
        $success = $lang->save();

        return Response::json(['success' => $success]);
    }
}
