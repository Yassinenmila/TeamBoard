<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Demande;

class DemandeController extends Controller
{
    /**
     * Liste des demandes
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Demande::with([
            'user',
            'commentaires.user'
        ]);

        // Admin / Responsable → toutes les demandes
        if (in_array($user->role, ['admin', 'responsable'])) {
            return response()->json($query->get());
        }

        // Membre → ses demandes uniquement
        return response()->json(
            $query->where('user_id', $user->id)->get()
        );
    }

    /**
     * Créer une demande
     */
    public function store(Request $request)
    {

        $request->validate([
            'type' => 'required|string',
            'description' => 'required|string',
            'message' => 'nullable|string'
        ]);

        $user = $request->user();

        $demande = Demande::create([
            'user_id' => $user->id,
            'type' => $request->type,
            'description' => $request->description,
            'status' => 'pending',
            'date' => now()
        ]);

        // 💬 commentaire initial
        if ($request->message) {
            $demande->commentaires()->create([
                'user_id' => $user->id,
                'contenu' => $request->message
            ]);
        }

        // 🔔 notifier admin/responsables
        $admins = \App\Models\User::whereIn('role', ['admin', 'responsable'])->get();

        foreach ($admins as $admin) {
            $demande->notifications()->create([
                'user_id' => $admin->id,
                'message' => "Nouvelle demande soumise : {$demande->type}",
                'lu' => false
            ]);
        }

        return response()->json([
            'message' => 'Demande créée avec succès',
            'demande' => $demande->load('commentaires.user')
        ], 201);
    }

    /**
     * Afficher une demande
     */
    public function show(Demande $demande)
    {
        return response()->json(
            $demande->load(['user', 'commentaires.user'])
        );
    }

    /**
     * Mise à jour
     */
    public function update(Demande $demande, Request $request)
    {
        $user = $request->user();
        $data = [];

        /**
         * ADMIN / RESPONSABLE → traiter la demande
         */
        if (in_array($user->role, ['admin', 'responsable'])) {
            $request->validate([
                'statut' => 'required|in:pending,accepted,rejected',
                'message' => 'nullable|string'
            ]);

            $data['status'] = match ($request->statut) {
                'accepted' => 'accepted',
                'rejected' => 'rejected',
                default => 'pending'
            };

            // 🔔 notifier membre
            $demande->notifications()->create([
                'user_id' => $demande->user_id,
                'message' => match ($request->statut) {
                    'accepted' => "Votre demande a été acceptée ✅",
                    'rejected' => "Votre demande a été refusée ❌",
                    default => "Votre demande est en cours de traitement"
                },
                'lu' => false
            ]);

            // 💬 commentaire réponse
            if ($request->message) {
                $demande->commentaires()->create([
                    'user_id' => $user->id,
                    'contenu' => $request->message
                ]);
            }
        }

        /**
         * MEMBRE → modifier sa demande si en attente
         */
        elseif ($user->id === $demande->user_id) {
            if ($demande->status !== 'pending') {
                return response()->json([
                    'message' => 'Vous ne pouvez plus modifier cette demande.'
                ], 403);
            }

            $request->validate([
                'description' => 'required|string',
                'message' => 'nullable|string'
            ]);

            $data['description'] = $request->description;

            if ($request->message) {
                $demande->commentaires()->create([
                    'user_id' => $user->id,
                    'contenu' => $request->message
                ]);
            }
        }

        if (empty($data)) {
            return response()->json([
                'message' => 'Non autorisé à modifier cette demande.'
            ], 403);
        }

        $demande->update($data);

        return response()->json([
            'message' => 'Demande mise à jour',
            'demande' => $demande->load('commentaires.user')
        ]);
    }

    /**
     * Supprimer une demande
     */
    public function destroy(Demande $demande, Request $request)
    {
        $user = $request->user();

        if (
            $user->id !== $demande->user_id &&
            !in_array($user->role, ['admin'])
        ) {
            return response()->json([
                'message' => 'Non autorisé'
            ], 403);
        }

        $demande->delete();

        return response()->json([
            'message' => 'Demande supprimée avec succès'
        ]);
    }

    public function traiter(Demande $demande, Request $request)
{
    $user = $request->user();

    // 🔒 Autorisation
    if (!in_array($user->role, ['admin', 'responsable'])) {
        return response()->json([
            'message' => 'Non autorisé'
        ], 403);
    }

    // ✅ Validation
    $request->validate([
        'statut' => 'required|in:accepted,rejected',
        'message' => 'nullable|string'
    ]);

    // ❌ Déjà traitée ?
    if ($demande->status !== 'pending') {
        return response()->json([
            'message' => 'Cette demande a déjà été traitée.'
        ], 400);
    }

    // 🔄 Mise à jour statut
    $demande->update([
        'status' => $request->statut
    ]);

    // 🔔 Notification au membre
    $demande->notifications()->create([
        'user_id' => $demande->user_id,
        'message' => $request->statut === 'accepted'
            ? "Votre demande a été acceptée ✅"
            : "Votre demande a été refusée ❌",
        'lu' => false
    ]);

    // 💬 Ajouter commentaire (optionnel)
    if ($request->message) {
        $demande->commentaires()->create([
            'user_id' => $user->id,
            'contenu' => $request->message
        ]);
    }

    return response()->json([
        'message' => 'Demande traitée avec succès',
        'demande' => $demande->load('commentaires.user')
    ]);
}
}
