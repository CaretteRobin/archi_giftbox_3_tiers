<?php

namespace gift\appli\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Theme extends Model
{
    protected $table = 'theme';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'libelle', 'description'
    ];

    public function coffretTypes(): HasMany
    {
        return $this->hasMany(CoffretType::class, 'theme_id');
    }

    /**
     * Méthode pour compter le nombre de coffrets dans ce thème
     */
    public function nombreCoffrets(): int
    {
        return $this->coffretTypes()->count();
    }

    /**
     * Méthode pour obtenir toutes les prestations associées à ce thème
     * (via les coffrets)
     */
    public function prestationsAssociees()
    {
        return Prestation::whereHas('coffretTypes', function($query) {
            $query->whereIn('coffret_id', $this->coffretTypes()->pluck('id'));
        })->get();
    }

}