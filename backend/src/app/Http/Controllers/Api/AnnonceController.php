<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Annonce;
use App\Models\User;

class AnnonceController extends Controller
{
    /**
     * Afficher toutes les annonces
     */
    public function index()
    {
        $annonces = Annonce::with('user')
            ->latest()
            ->get();

        return response()->json($annonces);
    }

    /**
     * Créer une annonce
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->role, ['admin', 'responsable'])) {
            return response()->json([
                'message' => 'Non autorisé'
            ], 403);
        }

        $request->validate([
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'type' => 'required|in:urgent,general'
        ]);

        $annonce = Annonce::create([
            'user_id' => $user->id,
            'titre' => $request->titre,
            'contenu' => $request->contenu,
            'type' => $request->type,
            'date_publication' => now()
        ]);

        /**
         * notification pour tout le monde
         */
        $users = User::where('id', '!=', $user->id)->get();

        foreach ($users as $membre) {
            $membre->notifications()->create([
                'message' => "Nouvelle annonce publiée : {$annonce->titre}",
                'lu' => false,
                'notifiable_id' => $annonce->id,
                'notifiable_type' => Annonce::class
            ]);
        }

        return response()->json([
            'message' => 'Annonce créée avec succès',
            'annonce' => $annonce
        ], 201);
    }

    /**
     * Voir une annonce spécifique
     */
    public function show(Annonce $annonce)
    {
        return response()->json(
            $annonce->load('user')
        );
    }

    /**
     * Modifier annonce
     */
    public function update(Request $request, Annonce $annonce)
    {
        $user = $request->user();

        if (
            $user->id !== $annonce->user_id &&
            $user->role !== 'admin'
        ) {
            return response()->json([
                'message' => 'Non autorisé'
            ], 403);
        }

        $request->validate([
            'titre' => 'sometimes|string|max:255',
            'contenu' => 'sometimes|string',
            'type' => 'sometimes|in:urgent,normal,important'
        ]);

        $annonce->update(
            $request->only([
                'titre',
                'contenu',
                'type'
            ])
        );

        return response()->json([
            'message' => 'Annonce mise à jour',
            'annonce' => $annonce
        ]);
    }

    /**
     * Supprimer annonce
     */
    public function destroy(Annonce $annonce, Request $request)
    {
        $user = $request->user();

        if (
            $user->id !== $annonce->user_id &&
            $user->role !== 'admin'
        ) {
            return response()->json([
                'message' => 'Non autorisé'
            ], 403);
        }

        $annonce->delete();

        return response()->json([
            'message' => 'Annonce supprimée'
        ]);
    }
}
