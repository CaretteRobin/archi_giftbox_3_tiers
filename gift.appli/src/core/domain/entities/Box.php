<?php

namespace gift\appli\core\domain\entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Box extends Model
{
    protected $table = 'box';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'token', 'libelle', 'description', 'montant', 'kdo',
        'message_kdo', 'statut', 'createur_id'
    ];

    /**
     * Constantes pour les statuts de box
     */
    const STATUT_CREE = 1;
    const STATUT_VALIDE = 2;
    const STATUT_PAYE = 3;
    const STATUT_LIVRE = 4;

    public function prestations(): BelongsToMany
    {
        return $this->belongsToMany(Prestation::class, 'box2presta', 'box_id', 'presta_id')
            ->withPivot('quantite');
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'createur_id');
    }

    /**
     * Scope pour filtrer par statut
     */
    public function scopeParStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }

    /**
     * Scope pour récupérer les boxes qui sont des cadeaux
     */
    public function scopeEstCadeau($query)
    {
        return $query->where('kdo', 1);
    }

    /**
     * Méthode pour calculer le montant total des prestations
     */
    public function calculerMontantTotal()
    {
        return $this->prestations()
            ->withPivot('quantite')
            ->get()
            ->sum(function ($prestation) {
                return $prestation->tarif * $prestation->pivot->quantite;
            });
    }

    /**
     * Méthode pour mettre à jour le montant de la box
     */
    public function updateMontant(): bool
    {
        $this->montant = $this->calculerMontantTotal();
        return $this->save();
    }

    /**
     * Accesseur pour obtenir le libellé du statut
     */
    public function getStatutLibelleAttribute(): string
    {
        $statuts = [
            self::STATUT_CREE => 'Créée',
            self::STATUT_VALIDE => 'Validée',
            self::STATUT_PAYE => 'Payée',
            self::STATUT_LIVRE => 'Livrée'
        ];

        return $statuts[$this->statut] ?? 'Inconnu';
    }
}