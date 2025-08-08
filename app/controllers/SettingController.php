<?php

namespace app\controllers;

use app\core\Config;
use app\core\Request;
use app\core\Response;
use app\core\UploadedFile;
use app\facades\App;
use app\models\Setting;
use app\models\SettingLang;
use Exception;

class SettingController
{
    public function index()
    {
        $settings = Setting::all();

        if (!$settings) {
            return render('admin.settings.create'); // 생성 폼
        }

        $setting = $settings[0];

        $langs = SettingLang::query()
            ->where('setting_id', '=', $setting->id)
            ->get() ?? [];


        return render('admin.settings.edit', [
            'data' => $setting->getAttributes(),
            'langs' => $langs,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $setting = new Setting();
            if (!$setting->save()) {
                throw new Exception('설정 저장에 실패했습니다.');
            }

            $this->saveLangs($setting->id, $request->input('langs', []));

            return Response::json(['success' => true, 'message' => '설정이 저장되었습니다.']);
        } catch (Exception $e) {
            return Response::json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function update(Request $request, int $id)
    {
        $setting = Setting::find($id);
        if (!$setting) {
            return Response::json(['success' => false, 'message' => '설정을 찾을 수 없습니다.']);
        }

        try {
            $setting->save(); // 갱신 시간 업데이트
            $this->saveLangs($setting->id, $request->input('langs', []));

            return Response::json(['success' => true, 'message' => '설정이 저장되었습니다.']);
        } catch (Exception $e) {
            return Response::json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function saveLangs(int $settingId, array $langs = []): void
    {
        $deleteImages = App::request()->input('delete_meta_image'); // 체크된 삭제값 받기

        foreach ($langs as $locale => $values) {
            if (!Config::get("locales")[$locale]) continue;

            $lang = SettingLang::query()
                ->where('setting_id', '=', $settingId)
                ->where('locale', '=', $locale)
                ->first();

            if ($lang) {
                $lang = new SettingLang($lang);
            } else {
                $lang = new SettingLang([
                    'setting_id' => $settingId,
                    'locale' => $locale
                ]);
            }

            $fields = [
                'tel', 'fax', 'address', 'youtube_link',
                'site_name', 'meta_title', 'meta_keywords', 'meta_description'
            ];

            foreach ($fields as $field) {
                $lang->$field = $values[$field] ?? '';
            }

            // 이미지 업로드
            $fileKey = "meta_image_$locale";
            $file = App::request()->file($fileKey);

            if ($file && $file->hasUploaded() && $file->isValid()) {
                if ($lang->meta_image) {
                    UploadedFile::delete($lang->meta_image);
                }
                $lang->meta_image = $file->move(UPLOAD_PATH . DS . 'settings');
            }

            // 이미지 삭제 체크 처리
            if (!empty($deleteImages[$locale])) {
                UploadedFile::delete($lang->meta_image);
                $lang->meta_image = '';
            }

            $lang->save();
        }
    }

}
