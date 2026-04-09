<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reunion;
use App\Models\User;


class ReunionController extends Controller
{
    /**
     * Affiche toutes les réunions selon le rôle de l'utilisateur
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Reunion::with([
            'creator',
            'invitations.user'
        ]);

        if (in_array($user->role, ['admin', 'responsable'])) {
            return response()->json($query->get());
        }

        // Pour un membre : réunion créées + réunion invité
        return response()->json(
            $query->where('user_id', $user->id)
                  ->orWhereHas('invitations', fn($q) => $q->where('user_id', $user->id))
                  ->get()
        );
    }

    /**
     * Crée une réunion et envoie des invitations si nécessaire
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if($user->role === 'membre') {
            return response()->json(['message' => 'Vous n’êtes pas autorisé à créer une réunion.'], 403);
        }

        $request->validate([
            'titre' => 'required|string',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'lieu' => 'nullable|string',
            'invitations' => 'nullable|array',
            'invitations.*' => 'exists:users,id'
        ]);

        $reunion = Reunion::create([
            'user_id' => $request->user()->id, // créateur
            'titre' => $request->titre,
            'description' => $request->description,
            'date' => $request->date,
            'lieu' => $request->lieu,
        ]);

        if ($request->invitations) {
            foreach ($request->invitations as $inv_id) {
                $reunion->invitations()->create([
                    'user_id' => $inv_id,
                    'statut' => 'en_attente'
                ]);

                // Créer une notification
                $userNotif = User::find($inv_id);
                $userNotif->notifications()->create([
                    'contenu' => "Vous êtes invité(e) à la réunion : {$reunion->titre}",
                    'type' => 'reunion',
                    'reunion_id' => $reunion->id,
                    'lu' => false
                ]);
            }
        }

        return response()->json([
            'message' => 'Réunion créée avec succès',
            'reunion' => $reunion->load('invitations.user')
        ], 201);
    }

    /**
     * Affiche une réunion spécifique
     */
    public function show(Reunion $reunion)
    {
        return response()->json(
            $reunion->load(['creator', 'invitations.user'])
        );
    }

    /**
     * Met à jour une réunion (seul le créateur)
     */
    public function update(Reunion $reunion, Request $request)
    {
        $user = $request->user();

        if ($user->id !== $reunion->user_id && $user->role !== 'admin') {
            return response()->json(['message' => 'Vous n’êtes pas autorisé à modifier cette réunion.'], 403);
        }

        $request->validate([
            'titre' => 'sometimes|string',
            'description' => 'nullable|string',
            'date' => 'sometimes|date',
            'lieu' => 'nullable|string',
            'invitations' => 'nullable|array',
            'invitations.*' => 'exists:users,id'
        ]);

        $reunion->update($request->only(['titre', 'description', 'date', 'lieu']));

        // Mise à jour des invitations si fourni
        if ($request->invitations) {
            $reunion->invitations()->delete(); // supprimer anciennes invitations
            $reunion->notifications()->delete(); // supprimer anciennes notifications
            foreach ($request->invitations as $inv_id) {
                $reunion->invitations()->create([
                    'user_id' => $inv_id,
                    'statut' => 'en_attente'
                ]);

                // Créer une notification
                $userNotif = User::find($inv_id);
                $userNotif->notifications()->create([
                    'contenu' => "Vous êtes invité(e) à la réunion : {$reunion->titre}",
                    'type' => 'reunion',
                    'reunion_id' => $reunion->id,
                    'lu' => false
                ]);
            }
        }

        return response()->json([
            'message' => 'Réunion mise à jour',
            'reunion' => $reunion->load('invitations.user')
        ]);
    }

    /**
     * Supprime une réunion (seul le créateur)
     */
    public function destroy(Reunion $reunion, Request $request)
    {
        $user = $request->user();

        if ($user->id !== $reunion->user_id && $user->role !== 'admin') {
            return response()->json(['message' => 'Vous n’êtes pas autorisé à supprimer cette réunion.'], 403);
        }

        $reunion->delete();
        $reunion->notifications()->delete(); // supprimer les notifications liées

        return response()->json([
            'message' => 'Réunion supprimée avec succès.'
        ]);
    }

    /**
     * Mettre à jour le statut d'une invitation
     */
    public function updateInvitation(Request $request, Reunion $reunion, $user_id)
    {
        $user = $request->user();

        if($user->id !== $user_id && $user->role !== 'admin') {
            return response()->json(['message' => 'Vous n’êtes pas autorisé à modifier cette invitation.'], 403);
        }

        $invitation = $reunion->invitations()->where('user_id', $user_id)->firstOrFail();

        $request->validate([
            'statut' => 'required|in:accepte,refuse,en_attente'
        ]);

        $invitation->update(['statut' => $request->statut]);

        return response()->json([
            'message' => 'Invitation mise à jour',
            'invitation' => $invitation
        ]);
    }
}
