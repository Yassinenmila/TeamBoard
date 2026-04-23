<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Livrable;

class LivrableController extends Controller
{
    /* =========================
        LISTE
    ========================= */
    public function index(Request $request)
{
    $user = $request->user();

    $query = Livrable::with(['user', 'tache', 'commentaires.user']);

    $livrables = in_array($user->role, ['admin', 'responsable'])
        ? $query->latest()->get()
        : $query->where('user_id', $user->id)->latest()->get();

    return response()->json($livrables);
}

    /* =========================
        SHOW
    ========================= */
    public function show(Livrable $livrable)
    {
        return response()->json(
            $livrable->load(['user', 'tache', 'commentaires.user'])
        );
    }

    /* =========================
        STORE (MEMBRE)
    ========================= */
    public function store(Request $request)
    {
        $request->validate([
            'tache_id' => 'required|exists:taches,id',
            'url' => 'required|string',
            'message' => 'nullable|string'
        ]);

        $user = $request->user();

        $livrable = Livrable::create([
            'tache_id' => $request->tache_id,
            'user_id' => $user->id,
            'url' => $request->url,
            'status' => 'in_progress',
        ]);

        if ($request->message) {
            $livrable->commentaires()->create([
                'user_id' => $user->id,
                'contenu' => $request->message
            ]);
        }

        return response()->json([
            'message' => 'Livrable créé',
            'livrable' => $livrable
        ]);
    }

    /* =========================
        UPDATE (FIX FINAL)
    ========================= */
    public function update(Livrable $livrable, Request $request)
{
    $user = $request->user();

    if (!in_array($user->role, ['admin', 'responsable'])) {
        return response()->json([
            'message' => 'Unauthorized'
        ], 403);
    }

    $request->validate([
        'status' => 'required|in:approved,rejected',
        'message' => 'required|string',
        'url' => 'sometimes|string'
    ]);

    // 🔥 sécurité métier
    if (!$livrable->url) {
        return response()->json([
            'message' => 'Impossible de valider un livrable vide'
        ], 422);
    }

    // ❌ déjà traité
    if (in_array($livrable->status, ['approved', 'rejected'])) {
        return response()->json([
            'message' => 'Livrable déjà traité'
        ], 409);
    }

    // =========================
    // 1. UPDATE STATUS
    // =========================
    $livrable->status = $request->status;

    if ($request->has('url')) {
        $livrable->url = $request->url;
    }

    $livrable->save();

    // =========================
    // 2. COMMENTAIRE ADMIN (RÉPONSE)
    // =========================
    $livrable->commentaires()->create([
        'user_id' => $user->id,
        'contenu' => $request->message
    ]);

    // =========================
    // 3. NOTIFICATION (OPTIONNEL)
    // =========================
    $livrable->notifications()->create([
        'user_id' => $livrable->user_id,
        'message' => $request->status === 'approved'
            ? 'Votre livrable a été validé ✅'
            : 'Votre livrable a été refusé ❌',
        'lu' => false
    ]);

    return response()->json([
        'message' => 'Livrable mis à jour avec succès',
        'livrable' => $livrable->load('commentaires.user')
    ]);
}

    /* =========================
        DELETE
    ========================= */
    public function destroy(Livrable $livrable, Request $request)
{
    $user = $request->user();

    if (
        $user->id !== $livrable->user_id &&
        !in_array($user->role, ['admin', 'responsable'])
    ) {
        return response()->json([
            'message' => 'Suppression interdite'
        ], 403);
    }

    $livrable->delete();

    return response()->json([
        'message' => 'Supprimé avec succès'
    ]);
}
}
