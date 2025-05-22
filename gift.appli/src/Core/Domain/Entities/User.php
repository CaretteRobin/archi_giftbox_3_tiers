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


    public function boxes(): HasMany
    {
        return $this->hasMany(Box::class, 'createur_id');
    }

    /**
     * Méthode pour vérifier si l'utilisateur est admin
     */
    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Scope pour filtrer par rôle
     */
    public function scopeParRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Méthode pour obtenir la valeur totale des boxes créées par l'utilisateur
     */
    public function totalValeurBoxes()
    {
        return $this->boxes()->sum('montant');
    }

    /**
     * Méthode pour obtenir le nombre de boxes dans chaque statut
     */
    public function statistiquesBoxes(): array
    {
        return $this->boxes()
            ->selectRaw('statut, count(*) as total')
            ->groupBy('statut')
            ->pluck('total', 'statut')
            ->toArray();
    }

}
