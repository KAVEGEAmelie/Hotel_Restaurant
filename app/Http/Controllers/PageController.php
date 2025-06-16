<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Mail\ContactFormMail;

class PageController extends Controller
{
    public function about()
    {
        return view('pages.about');
    }
    public function contact()
    {
        return view('pages.contact');
    }
    
    public function handleContactForm(Request $request)
{
    // 1. Valider les données
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'subject' => 'required|string|max:255',
        'message' => 'required|string',
    ]);

    // 2. Envoyer l'e-mail
    // Remplacez 'votre-email@domaine.com' par l'adresse où vous voulez recevoir les messages
    Mail::to('votre-email@domaine.com')->send(new ContactFormMail($validated));

    // 3. Rediriger avec un message de succès
    return redirect()->back()->with('success', 'Votre message a bien été envoyé ! Nous vous répondrons dès que possible.');
}
}
