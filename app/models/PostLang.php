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
        'created_at',
        'updated_at',
    ];
}
