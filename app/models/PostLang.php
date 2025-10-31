<?php 

namespace app\models;

use app\core\Model;

class PostLang extends Model
{
    protected string $table = 'no_post_langs';

    protected array $fillable = [
        'id',
        'post_id',
        'locale',
        'title',
        'content',
        'extra1', 'extra2', 'extra3', 'extra4', 'extra5',
        'extra6', 'extra7', 'extra8', 'extra9', 'extra10',
        'image1', 'image2', 'image3', 'image4', 'image5',
        'image6', 'image7', 'image8', 'image9', 'image10',
        'extra6', 'extra7', 'extra8', 'extra9', 'extra10',
        'image_label_1', 'image_label_2', 'image_label_3', 'image_label_4', 'image_label_5',
        'image_label_6', 'image_label_7', 'image_label_8', 'image_label_9', 'image_label_10',
        'created_at',
        'updated_at',
    ];
}
