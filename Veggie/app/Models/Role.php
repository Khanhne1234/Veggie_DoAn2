<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name'];
    //Thiêt lâp quan hệ n-n với Permission thông qua bảng trung gian RolePermission
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }
}
