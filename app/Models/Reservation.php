<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory; // Bonne pratique d'ajouter le trait HasFactory

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
        
        // Champs de paiement
        'statut_paiement',
        'montant_paye',
        'methode_paiement',
        'date_paiement',
        'notes_paiement',

        // Champs CashPay selon la documentation V2.0
        'cashpay_order_reference',
        'cashpay_merchant_reference',
        'cashpay_bill_url',
        'cashpay_code',
        'cashpay_qrcode_url',
        'cashpay_status',
        'cashpay_data',
        'cashpay_webhook_data',

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
        
        // Champs confirmation admin
        'admin_confirme_id',
        'date_confirmation',
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs (casting).
     *
     * @var array
     */
    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'client_date_naissance' => 'date',
        'piece_identite_delivree_le' => 'date',
        'date_confirmation' => 'datetime',
    ];

    /**
     * Relation : Une réservation appartient à un utilisateur.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation : Une réservation concerne une chambre.
     */
    public function chambre()
    {
        return $this->belongsTo(Chambre::class);
    }

    /**
     * Relation : L'admin qui a confirmé la réservation.
     */
    public function adminConfirme()
    {
        return $this->belongsTo(User::class, 'admin_confirme_id');
    }
}
