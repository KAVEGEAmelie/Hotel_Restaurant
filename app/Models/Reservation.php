<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Reservation extends Model
{
    use HasFactory;

    // --- Statuts de la réservation (une seule liste pour tout le projet) ---
    public const STATUT_EN_ATTENTE = 'en_attente';
    public const STATUT_CONFIRMEE = 'confirmee';
    public const STATUT_ANNULEE = 'annulee';
    public const STATUT_TERMINEE = 'terminee';

    public const STATUTS = [
        self::STATUT_EN_ATTENTE => 'En attente',
        self::STATUT_CONFIRMEE => 'Confirmée',
        self::STATUT_ANNULEE => 'Annulée',
        self::STATUT_TERMINEE => 'Terminée',
    ];

    /** Statuts qui bloquent la chambre sur la période réservée. */
    public const STATUTS_BLOQUANTS = [self::STATUT_EN_ATTENTE, self::STATUT_CONFIRMEE];

    // --- Statuts du paiement ---
    public const PAIEMENT_EN_ATTENTE = 'en_attente';
    public const PAIEMENT_PAYE = 'paye';
    public const PAIEMENT_ECHEC = 'echec';
    public const PAIEMENT_REMBOURSE = 'rembourse';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // Champs de base
        'user_id',
        'chambre_id',
        'check_in_date',
        'check_out_date',
        'nombre_invites',
        'prix_total',
        'statut',

        // Informations client
        'client_nom',
        'client_prenom',
        'client_email',
        'client_telephone',

        // Champs CinetPay
        'transaction_id',
        'payment_url',
        'payment_token',
        'operator_id',
        'statut_paiement',
        'montant_paye',
        'methode_paiement',
        'date_paiement',
        'notes_paiement',
        'cinetpay_payment_data',

        // Champs fiche de police (optionnels)
        'client_date_naissance',
        'client_nationalite',
        'client_profession',
        'client_domicile',
        'motif_voyage',
        'venant_de',
        'allant_a',
        'piece_identite_numero',
        'piece_identite_delivree_le',
        'piece_identite_delivree_a',
        'personne_a_prevenir',
    ];

    protected $hidden = [
        'access_token',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'date_paiement' => 'datetime',
        'client_date_naissance' => 'date',
        'piece_identite_delivree_le' => 'date',
    ];

    protected static function booted(): void
    {
        // Jeton secret utilisé dans les URL publiques (évite de deviner les réservations des autres).
        static::creating(function (Reservation $reservation) {
            $reservation->access_token ??= Str::random(48);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chambre()
    {
        return $this->belongsTo(Chambre::class);
    }

    /**
     * Réservations qui occupent la chambre sur la période [arrivée, départ[.
     */
    public function scopeChevauche(Builder $query, $checkin, $checkout): Builder
    {
        return $query->whereIn('statut', self::STATUTS_BLOQUANTS)
            ->where('check_in_date', '<', $checkout)
            ->where('check_out_date', '>', $checkin);
    }

    public function getNombreNuitsAttribute(): int
    {
        return (int) $this->check_in_date->diffInDays($this->check_out_date);
    }

    public function getStatutLabelAttribute(): string
    {
        return self::STATUTS[$this->statut] ?? ucfirst((string) $this->statut);
    }

    public function estPayee(): bool
    {
        return $this->statut_paiement === self::PAIEMENT_PAYE;
    }
}
