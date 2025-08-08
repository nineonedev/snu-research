<?php

namespace app\models;

use app\core\Model;

class TeamLang extends Model
{
    protected string $table = 'no_team_langs';

    protected array $fillable = [
        'id',
        'team_id',
        'locale',
        'name',
        'created_at',
        'updated_at',
    ];
}
