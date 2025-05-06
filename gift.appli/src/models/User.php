<?php

namespace gift\appli\models;

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

    public function boxes(): HasMany
    {
        return $this->hasMany(Box::class, 'createur_id');
    }
}