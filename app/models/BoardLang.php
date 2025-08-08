<?php 

namespace app\models;

use app\core\Model;

class BoardLang extends Model
{
    protected string $table = 'no_board_langs';

    protected array $fillable = [
        'id',
        'board_id',
        'locale',
        'name',
        'created_at',
        'updated_at',
    ];
}
