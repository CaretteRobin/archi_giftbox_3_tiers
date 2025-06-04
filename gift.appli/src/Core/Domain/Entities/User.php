<?php

namespace Gift\Appli\Core\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    protected $table = 'user';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'email', 'password', 'role'
    ];

    protected $hidden = [
        'password'
    ];

    const ROLE_CLIENT = 1;
    const ROLE_ADMIN = 100;


}
