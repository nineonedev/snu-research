<?php 

namespace app\models;

use app\core\Model;

class Board extends Model
{
    protected string $table = 'no_boards';

    protected array $fillable = [
        'id',
        'team_id',
        'skin',
        'search_key', 
        'image',
        'is_public',
        'extra1', 'extra2', 'extra3', 'extra4', 'extra5',
        'extra6', 'extra7', 'extra8', 'extra9', 'extra10',
        'created_at',
        'updated_at',
    ];
}
