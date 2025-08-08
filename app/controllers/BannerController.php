<?php

namespace app\controllers;

use app\core\Config;
use app\core\Request;
use app\core\Response;
use app\core\UploadedFile;
use app\models\Banner;
use app\models\BannerLang;
use Exception;

class BannerController
{
    public function index(Request $request)
    {
        $page = (int)($request->input('page') ?? 1);
        $paginator = Banner::paginate(10, $page)->withNumbers();

        return render('admin.banners.index', [
            'rows' => $paginator->toArray()['data'],
            'pagination' => $paginator->render(),
        ]);
    }

    public function create()
    {
        return render('admin.banners.create');
    }

    public function store(Request $request)
    {
        try {
            $banner = new Banner([
                'type' => $request->input('type'),
                'is_hidden' => $request->input('is_hidden', 0),
                'display_order' => $request->input('display_order', 0),
                'image' => $this->handleImageUpload($request),
            ]);

            if (!$banner->save()) throw new Exception('배너 생성 실패');

            $this->saveLangs($banner->id, $request->input('langs', []));

            return Response::json(['success' => true, 'message' => '생성 완료', 'data' => $banner->getAttributes()]);
        } catch (Exception $e) {
            return Response::json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function edit(Request $request, int $id)
    {
        $banner = Banner::find($id);
        if (!$banner) return Response::back('배너를 찾을 수 없습니다.');

        $langs = BannerLang::query()->where('banner_id', '=', $id)->get();
        $banner->langs = $langs;

        return render('admin.banners.edit', ['data' => $banner->getAttributes()]);
    }

    public function update(Request $request, int $id)
    {
        $banner = Banner::find($id);
        if (!$banner) return Response::back('배너를 찾을 수 없습니다.');

        try {
            $banner->type = $request->input('type');
            $banner->is_hidden = $request->input('is_hidden', 0);
            $banner->display_order = $request->input('display_order', 0);

            $image = $this->handleImageUpload($request, $banner->image);
            if ($image !== null) $banner->image = $image;

            if ($request->input('delete_image')) {
                UploadedFile::delete($banner->image);
                $banner->image = '';
            }

            if (!$banner->save()) throw new Exception('수정 실패');

            $this->saveLangs($banner->id, $request->input('langs', []));

            return Response::json(['success' => true, 'message' => '수정 완료', 'data' => $banner->getAttributes()]);
        } catch (Exception $e) {
            return Response::json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function destroy(Request $request, int $id)
    {
        $banner = Banner::find($id);
        if (!$banner) return Response::back('배너를 찾을 수 없습니다.');

        if (!$banner->delete()) {
            return Response::json(['success' => false, 'message' => '삭제 실패']);
        }

        if ($banner->image && !UploadedFile::delete($banner->image)) {
            return Response::json(['success' => false, 'message' => '파일 삭제 실패']);
        }

        return Response::json(['success' => true, 'message' => '삭제 완료']);
    }

    // helpers
    private function handleImageUpload(Request $request, ?string $existingPath = null): ?string
    {
        $image = $request->file('image');
        if (!($image instanceof UploadedFile) || !$image->hasUploaded()) return null;
        if (!$image->isValid() || !$image->isAllowedMimeType('image')) throw new \Exception('유효하지 않은 이미지입니다.');

        if ($existingPath) UploadedFile::delete($existingPath);
        return $image->move(UPLOAD_PATH . DS . 'banners');
    }

    private function saveLangs(int $bannerId, array $langs = [])
    {
        foreach ($langs as $locale => $values) {
            $lang = BannerLang::query()
                ->where('banner_id', '=', $bannerId)
                ->where('locale', '=', $locale)
                ->first();

            $data = [
                'title' => $values['title'] ?? '',
                'description' => $values['description'] ?? '',
                'link' => $values['link'] ?? '',
            ];

            if ($lang) {
                $lang = new BannerLang($lang);
                foreach ($data as $key => $val) {
                    $lang->$key = $val;
                }
                $lang->save();
            } else {
                BannerLang::create(array_merge($data, [
                    'banner_id' => $bannerId,
                    'locale' => $locale
                ]));
            }
        }
    }
}
