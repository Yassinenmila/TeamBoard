<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Livrable;

class LivrableController extends Controller
{
    /**
     * Liste des livrables
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Livrable::with([
            'user',
            'tache',
            'commentaires.user'
        ]);

        // Admin / Responsable → tout voir
        if (in_array($user->role, ['admin', 'responsable'])) {
            return response()->json($query->get());
        }

        // Membre → seulement ses livrables
        return response()->json(
            $query->where('user_id', $user->id)->get()
        );
    }

    /**
     * Création d’un livrable
     */
    public function store(Request $request)
    {
        $request->validate([
            'tache_id' => 'required|exists:taches,id',
            'type' => 'required|string',
            'url' => 'required|string',
            'message' => 'nullable|string'
        ]);

        $user = $request->user();

        // Création du livrable
        $livrable = Livrable::create([
            'tache_id' => $request->tache_id,
            'user_id' => $user->id,
            'type' => $request->type,
            'url' => $request->url,
            'status' => 'in_progress',
        ]);

        // Message initial (commentaire)
        if ($request->message) {
            $livrable->commentaires()->create([
                'user_id' => $user->id,
                'contenu' => $request->message
            ]);
        }

        // 🔔 Notification au responsable (créateur de la tâche)
        $createur = $livrable->tache->createur;

        if ($createur && $createur->id !== $user->id) {
            $livrable->notifications()->create([
                'user_id' => $createur->id,
                'message' => "Nouveau livrable soumis pour la tâche : {$livrable->tache->titre}",
                'lu' => false
            ]);
        }

        return response()->json([
            'message' => 'Livrable créé avec succès, en attente de validation',
            'livrable' => $livrable->load('commentaires.user')
        ], 201);
    }

    /**
     * Afficher un livrable
     */
    public function show(Livrable $livrable)
    {
        return response()->json(
            $livrable->load([
                'user',
                'tache',
                'commentaires.user'
            ])
        );
    }

    /**
     * Mise à jour
     */
    public function update(Livrable $livrable, Request $request)
    {
        $user = $request->user();
        $data = [];

        /**
         * ADMIN / RESPONSABLE
         */
        if (in_array($user->role, ['admin', 'responsable'])) {

            $request->validate([
                'status' => 'required|in:in_progress,valide,rejete',
                'message' => 'nullable|string'
            ]);

            $data['status'] = $request->status;

            // 🔔 Notification au membre
            if (in_array($request->status, ['valide', 'rejete'])) {
                $livrable->notifications()->create([
                    'user_id' => $livrable->user_id,
                    'message' => $request->status === 'valide'
                        ? "Votre livrable a été validé ✅"
                        : "Votre livrable a été rejeté ❌",
                    'lu' => false
                ]);
            }

            // 💬 commentaire du responsable
            if ($request->message) {
                $livrable->commentaires()->create([
                    'user_id' => $user->id,
                    'contenu' => $request->message
                ]);
            }
        }

        /**
         * MEMBRE
         */
        if ($user->id === $livrable->user_id) {

            // interdit si validé/rejeté
            if ($livrable->status !== 'in_progress') {
                return response()->json([
                    'message' => 'Vous ne pouvez plus modifier ce livrable.'
                ], 403);
            }

            $request->validate([
                'url' => 'required|string',
                'type' => 'sometimes|string',
                'message' => 'nullable|string'
            ]);

            $data['url'] = $request->url;

            if ($request->has('type')) {
                $data['type'] = $request->type;
            }

            // 💬 commentaire du membre
            if ($request->message) {
                $livrable->commentaires()->create([
                    'user_id' => $user->id,
                    'contenu' => $request->message
                ]);
            }

            // 🔔 notifier responsable modification
            $createur = $livrable->tache->createur;

            if ($createur && $createur->id !== $user->id) {
                $livrable->notifications()->create([
                    'user_id' => $createur->id,
                    'message' => "Le livrable a été modifié pour la tâche : {$livrable->tache->titre}",
                    'lu' => false
                ]);
            }
        }

        if (!empty($data)) {
            $livrable->update($data);
        }

        return response()->json([
            'message' => 'Livrable mis à jour',
            'livrable' => $livrable->load('commentaires.user')
        ]);
    }

    /**
     * Suppression
     */
    public function destroy(string $id, Request $request)
    {
        $livrable = Livrable::findOrFail($id);
        $user = $request->user();

        if (
            $user->id !== $livrable->user_id &&
            !in_array($user->role, ['admin', 'responsable']) &&
            $livrable->status !== 'in_progress'
        ) {
            return response()->json([
                'message' => 'Vous n\'avez pas la permission de supprimer ce livrable.'
            ], 403);
        }

        $livrable->delete();

        return response()->json([
            'message' => 'Livrable supprimé avec succès.'
        ]);
    }
}
