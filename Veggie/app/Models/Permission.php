<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['name'];
    //Thiêt lâp quan hệ n-n với Role thông qua bảng trung gian RolePermission
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }
}
