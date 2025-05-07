<?php

namespace gift\appli\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CoffretType extends Model
{
    protected $table = 'coffret_type';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'libelle', 'description', 'theme_id'
    ];

    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class, 'theme_id');
    }

    public function prestations(): BelongsToMany
    {
        return $this->belongsToMany(Prestation::class, 'coffret2presta', 'coffret_id', 'presta_id');
    }

    /**
     * Méthode pour obtenir le nombre de prestations associées
     */
    public function nombrePrestations(): int
    {
        return $this->prestations()->count();
    }

    /**
     * Méthode pour obtenir le prix estimé du coffret (somme des prestations)
     */
    public function prixEstime()
    {
        return $this->prestations()->sum('tarif');
    }

    /**
     * Méthode pour vérifier si le coffret contient une prestation spécifique
     */
    public function contientPrestation($prestationId): bool
    {
        return $this->prestations()->where('id', $prestationId)->exists();
    }

}