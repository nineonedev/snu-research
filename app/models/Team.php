<?php 

namespace app\models;

use app\core\Model;

class Team extends Model
{
    protected string $table = 'no_teams';

    protected array $fillable = [
        'id',
        'is_hidden',
        'image',
        'created_at',
        'updated_at',
    ];
}
