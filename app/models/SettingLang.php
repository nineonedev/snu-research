<?php 

namespace app\models;

use app\core\Model;

class SettingLang extends Model
{
    protected string $table = 'no_setting_langs';

    protected array $fillable = [
        'id',
        'setting_id',
        'locale',
        'tel',
        'fax',
        'address',
        'youtube_link',
        'site_name',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'meta_image',
        'created_at',
        'updated_at',
    ];
}
