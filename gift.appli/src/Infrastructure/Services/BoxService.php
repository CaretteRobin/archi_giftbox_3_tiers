<?php

namespace Gift\Appli\Core\Application\Services;

use Gift\Appli\Core\Application\Exceptions\BoxException;
use Gift\Appli\Core\Application\Usecases\BoxServiceInterface;
use Gift\Appli\Core\Domain\Entities\Box;
use Gift\Appli\Core\Domain\Entities\Prestation;
use Gift\Appli\Core\Domain\Entities\User;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

class BoxService implements BoxServiceInterface
{
    /**
     * Crée une nouvelle box vide
     *
     * @param string $name Nom de la box
     * @param string $description Description de la box
     * @param bool $isGift Indique si c'est un cadeau (true) ou non (false)
     * @param string|null $giftMessage Message en cas de cadeau (obligatoire si isGift=true)
     * @param string $creatorId Identifiant de l'utilisateur créateur
     *
     * @return Box La box créée qui devient la box courante
     *
     * @throws BoxException Si les données fournies sont invalides
     */
    public function createBox(string $name, string $description, bool $isGift, ?string $giftMessage, string $creatorId): Box
    {
        // Vérification des données d'entrée
        if (empty($name) || empty($description)) {
            throw new BoxException('Le nom et la description sont obligatoires', BoxException::INVALID_DATA);
        }

        if ($isGift && empty($giftMessage)) {
            throw new BoxException('Le message cadeau est obligatoire pour une box cadeau', BoxException::INVALID_DATA);
        }

        // Vérification que l'utilisateur existe
        $user = User::find($creatorId);
        if (!$user) {
            throw new BoxException('Utilisateur inconnu', BoxException::UNAUTHORIZED_USER);
        }

        // Création de la box
        $box = new Box();
        $box->id = (string) Str::uuid();
        $box->libelle = $name;
        $box->description = $description;
        $box->kdo = $isGift ? 1 : 0;
        $box->message_kdo = $isGift ? $giftMessage : '';
        $box->montant = 0;
        $box->statut = Box::STATUT_CREE;
        $box->createur_id = $creatorId;
        $box->created_at = Date::now();
        $box->updated_at = Date::now();

        if (!$box->save()) {
            throw new BoxException('Erreur lors de la création de la box', BoxException::INVALID_DATA);
        }

        return $box;
    }

    /**
     * Ajoute une prestation à la box courante
     *
     * @param string $boxId Identifiant de la box
     * @param string $prestationId Identifiant de la prestation à ajouter
     * @param int $quantity Quantité de prestations à ajouter (défaut: 1)
     *
     * @return Box La box mise à jour
     *
     * @throws BoxException Si la box n'existe pas, n'est plus modifiable ou si la prestation n'existe pas
     */
    public function addPrestationToBox(string $boxId, string $prestationId, int $quantity = 1): Box
    {
        // Vérification des données d'entrée
        if ($quantity <= 0) {
            throw new BoxException('La quantité doit être supérieure à 0', BoxException::INVALID_QUANTITY);
        }

        // Récupération de la box
        $box = Box::find($boxId);
        if (!$box) {
            throw new BoxException('Box introuvable', BoxException::INVALID_BOX);
        }

        // Vérification que la box est modifiable
        if ($box->statut !== Box::STATUT_CREE) {
            throw new BoxException('Cette box n\'est plus modifiable', BoxException::BOX_NOT_MODIFIABLE);
        }

        // Vérification que la prestation existe
        $prestation = Prestation::find($prestationId);
        if (!$prestation) {
            throw new BoxException('Prestation introuvable', BoxException::INVALID_PRESTATION);
        }

        // Vérification si la prestation existe déjà dans la box
        $existingPrestation = $box->prestations()->where('presta_id', $prestationId)->first();

        if ($existingPrestation) {
            // Mise à jour de la quantité
            $newQuantity = $existingPrestation->pivot->quantite + $quantity;
            $box->prestations()->updateExistingPivot($prestationId, ['quantite' => $newQuantity]);
        } else {
            // Ajout de la nouvelle prestation
            $box->prestations()->attach($prestationId, ['quantite' => $quantity]);
        }

        // Mise à jour du montant total
        $box->updateMontant();
        $box->updated_at = Date::now();
        $box->save();

        return $box;
    }

    /**
     * Récupère les détails d'une box spécifique
     *
     * @param string $boxId Identifiant de la box
     *
     * @return array Détails de la box (prestations, tarifs, montant total, état)
     *
     * @throws BoxException Si la box n'existe pas
     */
    public function getBoxDetails(string $boxId): array
    {
        // Récupération de la box avec ses prestations
        $box = Box::with(['prestations', 'createur'])->find($boxId);

        if (!$box) {
            throw new BoxException('Box introuvable', BoxException::INVALID_BOX);
        }

        // Préparation des détails des prestations
        $prestations = [];
        foreach ($box->prestations as $prestation) {
            $prestations[] = [
                'id' => $prestation->id,
                'libelle' => $prestation->libelle,
                'description' => $prestation->description,
                'tarif' => $prestation->tarif,
                'tarif_formate' => $prestation->getTarifFormateAttribute(),
                'quantite' => $prestation->pivot->quantite,
                'sous_total' => $prestation->tarif * $prestation->pivot->quantite,
                'image' => $prestation->getImageUrl()
            ];
        }

        // Construction du résultat
        return [
            'id' => $box->id,
            'libelle' => $box->libelle,
            'description' => $box->description,
            'montant' => $box->montant,
            'kdo' => (bool) $box->kdo,
            'message_kdo' => $box->message_kdo,
            'statut' => $box->statut,
            'statut_libelle' => $box->getStatutLibelleAttribute(),
            'createur' => [
                'id' => $box->createur->id,
                'email' => $box->createur->email
            ],
            'prestations' => $prestations,
            'nombre_prestations' => count($prestations),
            'created_at' => $box->created_at,
            'updated_at' => $box->updated_at
        ];
    }

    /**
     * Valide une box
     *
     * @param string $boxId Identifiant de la box
     * @param string $userId Identifiant de l'utilisateur demandant la validation
     *
     * @return Box La box validée
     *
     * @throws BoxException Si la box ne peut pas être validée ou si l'utilisateur n'est pas autorisé
     */
    public function validateBox(string $boxId, string $userId): Box
    {
        // Récupération de la box
        $box = Box::find($boxId);

        if (!$box) {
            throw new BoxException('Box introuvable', BoxException::INVALID_BOX);
        }

        // Vérification que l'utilisateur est le créateur de la box
        if ($box->createur_id !== $userId) {
            throw new BoxException('Seul le créateur de la box peut la valider', BoxException::UNAUTHORIZED_USER);
        }

        // Vérification que la box est modifiable
        if ($box->statut !== Box::STATUT_CREE) {
            throw new BoxException('Cette box n\'est pas en état d\'être validée', BoxException::BOX_NOT_MODIFIABLE);
        }

        // Vérification que la box contient au moins 2 prestations
        $prestationCount = $box->prestations()->count();
        if ($prestationCount < 2) {
            throw new BoxException('La box doit contenir au moins 2 prestations pour être validée', BoxException::INSUFFICIENT_PRESTATIONS);
        }

        // Mise à jour du statut
        $box->statut = Box::STATUT_VALIDE;
        $box->updated_at = Date::now();
        $box->save();

        return $box;
    }
}