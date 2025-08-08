<?php

use app\core\Disk;
use app\core\Validator;
use app\entities\PostEntity;
use app\facades\App;
use app\facades\Rule;
use app\core\File;
use app\core\Request;
use app\facades\Storage;
use app\lib\rules\types\FileImageRule;
use app\models\Post;

require_once '../bootstrap/app.php';
// $vt = Validator::make(
//     ['thumb_image' => $_FILES['thumb_image']], 
//     ['thumb_image' => [Rule::fileUpload(), Rule::fileNotEmpty(), Rule::fileSize(2), Rule::image()]]
// );

// if ($vt->fails()) {
//     dd($vt->getErrors(), 0);
// } else {
//     echo 'success';
// }

$disk = new Disk(UPLOAD_PATH.DS.'posts'); 

$request = App::request();

if ($request->isPost()) {


    // $susccess = $disk->delete('4b6f4584033c806a.jpg');

    // dd($susccess, 0);

    Post::create();

    // if (File::has('thumb_image')) {
    //     $file = File::make('thumb_image', 'file');

    //     $success = $disk->put($file);

    //     dd($success, 0);

    //     if ($success) {
    //         dd($disk->lastPutData(), 0);
    //     } else {
    //         dd($file->getErrors(), 0);
    //     }
    // }



    // $entity = new PostEntity();
    // $entity->fill([
    //     'title' => $request->body('title'), 
    //     'content' => $request->body('content'), 
    //     'thumb_image' => $request->file('thumb_image')
    // ]);
    
    // if($entity->validate()) {
        
    //     $data = $entity->getFilterData();

        
    //     if ($entity->isFileRule('thumb_image')) {
    //         $fileData = $data['thumb_image'];

    //         if (File::isMissingUpload($fileData)) {
    //             // db에서 파일 확인 후 있으면 무시, 없으면 그냥 null로 세팅
    //             $hasFile = 0; 

    //             $fileData  = $hasFile ? $fileData : null;
    //         } else {
    //             $file = new File($fileData);
    //             Storage::put($file); 
    //         }

    //         dd($fileData, 0);
    //     }

    // } else {
    //     dd($entity->getErrors(), 0);
    // }
}



$posts = Post::all();

?>
<div class="no-container-md">
    <h1 class="no-heading-lg --tac">게시글</h1>

    <form id="login-form" method="post" enctype="multipart/form-data">
        
        <div class="no-form-control">
            <label for="title">제목</label>
            <input type="text" name="title" id="title" placeholder="제목">
        </div>
        <div class="no-form-control">
            <label for="content">내용</label>
            <textarea name="content" id="content" placeholder="내용"></textarea>
        </div>
        <div class="no-form-control">
            <label for="thumb_image">이미지</label>
            <input type="file" name="thumb_image" id="thumb_image" placeholder="이미지">
        </div>
        <button type="submit" class="no-btn no-btn-primary">전송</button>
    </form>
</div>