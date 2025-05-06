<?php

namespace gift\appli\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categorie extends Model
{
    protected $table = 'categorie';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'libelle', 'description'
    ];

    public function prestations(): HasMany
    {
        return $this->hasMany(Prestation::class, 'cat_id');
    }
}