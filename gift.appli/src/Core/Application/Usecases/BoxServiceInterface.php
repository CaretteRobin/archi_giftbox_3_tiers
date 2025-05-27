<?php

namespace Gift\Appli\Core\Application\Usecases;

use Gift\Appli\Core\Domain\Entities\Box;
use Gift\Appli\Core\Application\Exceptions\BoxException;

interface BoxServiceInterface
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
    public function createBox(string $name, string $description, bool $isGift, ?string $giftMessage, string $creatorId): Box;

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
    public function addPrestationToBox(string $boxId, string $prestationId, int $quantity = 1): Box;

    /**
     * Récupère les détails d'une box spécifique
     *
     * @param string $boxId Identifiant de la box
     *
     * @return array Détails de la box (prestations, tarifs, montant total, état)
     *
     * @throws BoxException Si la box n'existe pas
     */
    public function getBoxDetails(string $boxId): array;

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
    public function validateBox(string $boxId, string $userId): Box;
}