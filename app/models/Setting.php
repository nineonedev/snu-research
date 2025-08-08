<?php 

namespace app\models;

use app\core\Model;

class Setting extends Model
{
    protected string $table = 'no_settings';

    protected array $fillable = [
        'id',
        'created_at',
        'updated_at',
    ];
}
