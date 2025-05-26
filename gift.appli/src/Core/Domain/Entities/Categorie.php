<?php

namespace Gift\Appli\Core\Domain\Entities;

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
        return $this->hasMany(Prestation::class, 'categorie_id');
    }

    /**
     * Méthode pour obtenir le nombre de prestations dans la catégorie
     */
    public function nombrePrestations(): int
    {
        return $this->prestations()->count();
    }

    /**
     * Méthode pour obtenir la prestation la moins chère de la catégorie
     */
    public function prestationMoinsChere()
    {
        return $this->prestations()->orderBy('tarif', 'asc')->first();
    }

    /**
     * Méthode pour obtenir la prestation la plus chère de la catégorie
     */
    public function prestationPlusChere()
    {
        return $this->prestations()->orderBy('tarif', 'desc')->first();
    }

    /**
     * Méthode pour obtenir le prix moyen des prestations de la catégorie
     */
    public function prixMoyen()
    {
        return $this->prestations()->avg('tarif');
    }

}
