<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Livrable;
use Termwind\Components\Li;

class LivrableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request){

        $user = $request->user();

        $query = Livrable::with([
            'user',
            'tache',
            'commentaires.user'
        ]);

        if (in_array($user->role, ['admin', 'responsable'])) {
            return response()->json($query->get());
        }

        return response()->json(
            $query->where('user_id', $user->id)->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tache_id' => 'required|exists:taches,id',
            'type' => 'required|string',
            'url' => 'required|string',
            'message' => 'nullable|string'
        ]);

        // Création du livrable avec statut initial
        $livrable = Livrable::create([
            'tache_id' => $request->tache_id,
            'user_id' => $request->user()->id,
            'type' => $request->type,
            'url' => $request->url,
            'status' => 'in_progress', // <-- ici
        ]);

        // Ajouter le message initial si fourni
        if ($request->message) {
            $livrable->commentaires()->create([
                'user_id' => $request->user()->id,
                'contenu' => $request->message
            ]);
        }


        return response()->json([
            'message' => 'Livrable créé avec succès , veuiller attendre la validation',
            'livrable' => $livrable->load('commentaires.user')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Livrable $livrable){

        return response()->json(
            $livrable->load([
                'user',
                'tache',
                'commentaires.user'
            ])
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Livrable $livrable, Request $request)
    {
        $user = $request->user();
        $data = [];

        // Admin / Responsable peut changer le statut et ajouter un commentaire
        if (in_array($user->role, ['admin', 'responsable'])) {
            $request->validate([
                'statuts' => 'required|in:in_progress,valide,rejete'
            ]);

            $data['statuts'] = $request->statuts;

            if ($request->message) {
                $livrable->commentaires()->create([
                    'user_id' => $user->id,
                    'contenu' => $request->message
                ]);
            }
        }


        // Membre ne peut modifier que son propre livrable et si c'est encore in_progress
        if ($user->id === $livrable->user_id) {
            if ($livrable->statuts !== 'in_progress') {
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

            // Ajouter message du membre
            if ($request->message) {
                $livrable->commentaires()->create([
                    'user_id' => $user->id,
                    'contenu' => $request->message
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request)
    {
        $livrable = Livrable::findOrFail($id);
        $user = $request->user();
        if ($user->id !== $livrable->user_id && !in_array($user->role, ['admin', 'responsable']) && $livrable->status !== 'in_progress') {
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
