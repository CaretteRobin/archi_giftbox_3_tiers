<?php

namespace gift\appli\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Prestation extends Model
{
    protected $table = 'prestation';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'libelle', 'description', 'url', 'unite',
        'tarif', 'img', 'cat_id'
    ];

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'cat_id');
    }

    public function boxes(): BelongsToMany
    {
        return $this->belongsToMany(Box::class, 'box2presta', 'presta_id', 'box_id')
            ->withPivot('quantite');
    }

    public function coffretTypes(): BelongsToMany
    {
        return $this->belongsToMany(CoffretType::class, 'coffret2presta', 'presta_id', 'coffret_id');
    }

    /**
     * Scope pour filtrer les prestations par catégorie
     */
    public function scopeParCategorie($query, $categorieId)
    {
        return $query->where('cat_id', $categorieId);
    }

    /**
     * Scope pour filtrer les prestations par tarif
     */
    public function scopeTarifInferieur($query, $prix)
    {
        return $query->where('tarif', '<=', $prix);
    }

    /**
     * Accesseur pour formater le tarif avec l'unité
     */
    public function getTarifFormateAttribute(): string
    {
        return $this->tarif . ' ' . ($this->unite ?? '€');
    }

    /**
     * Méthode pour obtenir l'image avec chemin complet
     */
    public function getImageUrl($basePath = '/img/'): string
    {
        return $basePath . $this->img;
    }
}