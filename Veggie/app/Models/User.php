<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $fillable = ['name', 'email', 'password', 'status'
    , 'phone_number', 'avatar', 'address','role_id', 'activation_token', 'google_id'
    ];
    //Thiêt lâp quan hệ n-n với Role thông qua bảng trung gian UserRole
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public function shippingAddresses()
    {
        return $this->hasMany(ShippingAddress::class);
    }
    //Check status của user
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isBanned()
    {
        return $this->status === 'banned';
    }

    public function isDeleted()
    {
        return $this->status === 'deleted';
    }

    public function getAvatarUrl()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return asset('assets/clients/img/default-avatar.png');
    }
}
