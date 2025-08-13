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
        
        // Champs CinetPay
        'transaction_id',
        'payment_url',
        'payment_token', 
        'payment_status',
        'payment_date',
        'payment_method',
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
}
