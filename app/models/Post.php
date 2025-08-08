<?php 

namespace app\models;

use app\core\Model;

class Post extends Model
{
    protected string $table = 'no_posts';

    protected array $fillable = [
        'id',
        'board_id',
        'is_hidden',
        'is_notice',
        'views',
        'link_url',
        'image',
        'created_at',
        'updated_at',
    ];
}
