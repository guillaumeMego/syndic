<?php 

namespace App\Enum;

final class EtatProblematiqueEnum
{
    public const EN_ATTENTE = 'En attente de validation';
    public const EN_COURS = 'En cours';
    public const RESOLU = 'Résolu';
    public const NON_RESOLU = 'Non résolu';

    public static function getEtats(): array
    {
        return [
            self::EN_ATTENTE,
            self::EN_COURS,
            self::RESOLU,
            self::NON_RESOLU,
        ];
    }
}