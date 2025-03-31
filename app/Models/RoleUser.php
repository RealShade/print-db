<?php

namespace App\Models;

use App\Models\Traits\HasUser;
use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model
{

    use HasUser;

    public const PERMISSION_ROLE_TABLE = 'permission_role';

    public $timestamps = false;

    protected $table = 'role_user';

    protected $fillable = [
        'user_id',
        'role_id',
    ];
}
