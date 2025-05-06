<?php

namespace gift\appli\models;

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

    public function prestations(): BelongsToMany
    {
        return $this->belongsToMany(Prestation::class, 'box2presta', 'box_id', 'presta_id')
            ->withPivot('quantite');
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'createur_id');
    }
}