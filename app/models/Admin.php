<?php

namespace app\models;

use app\core\Model;

class Admin extends Model
{
    protected string $table = 'no_admins';

    protected array $fillable = [
        'id',
        'team_id',
        'role_id',
        'name',
        'username',
        'password',
        'created_at',
        'updated_at',
    ];
}
