<?php

namespace app\models;

use app\core\Model;

class BannerLang extends Model
{
    protected string $table = 'no_banner_langs';

    protected array $fillable = [
        'id',
        'banner_id',
        'locale',
        'title',
        'description',
        'link',
        'created_at',
        'updated_at',
    ];

}
