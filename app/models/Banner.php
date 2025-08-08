<?php

namespace app\models;

use app\core\Model;

class Banner extends Model
{
    protected string $table = 'no_banners';

    protected array $fillable = [
        'id',
        'type',
        'image',
        'is_hidden',
        'display_order',
        'created_at',
        'updated_at',
    ];
}
