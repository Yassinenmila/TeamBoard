<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Presence;
use Carbon\Carbon;

class PresenceController extends Controller
{
    /**
     * Liste des présences
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Presence::with('user');

        // Admin / Responsable → tout voir
        if (in_array($user->role, ['admin', 'responsable'])) {
            return response()->json($query->latest()->get());
        }

        // Membre → ses présences
        return response()->json(
            $query->where('user_id', $user->id)->latest()->get()
        );
    }

    /**
     * Pointer (entrée)
     */
    public function checkIn(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today();

        // Vérifier si déjà pointé aujourd’hui
        $existing = Presence::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Vous avez déjà pointé aujourd’hui.'
            ], 400);
        }

        $now = Carbon::now();

        // Déterminer état (exemple : retard après 09:00)
        $etat = $now->hour > 9 ? 'retard' : 'present';

        $presence = Presence::create([
            'user_id' => $user->id,
            'date' => $today,
            'heure_entree' => $now,
            'etat' => $etat
        ]);

        return response()->json([
            'message' => 'Entrée enregistrée',
            'presence' => $presence
        ]);
    }

    /**
     * Pointer sortie
     */
    public function checkOut(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today();

        $presence = Presence::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if (!$presence) {
            return response()->json([
                'message' => 'Vous devez pointer l’entrée d’abord.'
            ], 400);
        }

        if ($presence->heure_sortie) {
            return response()->json([
                'message' => 'Sortie déjà enregistrée.'
            ], 400);
        }

        $presence->update([
            'heure_sortie' => Carbon::now()
        ]);

        return response()->json([
            'message' => 'Sortie enregistrée',
            'presence' => $presence
        ]);
    }

    /**
     * Voir une présence spécifique
     */
    public function show(Presence $presence, Request $request)
    {
        $user = $request->user();

        if (
            $user->role !== 'admin' &&
            $user->role !== 'responsable' &&
            $user->id !== $presence->user_id
        ) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        return response()->json($presence->load('user'));
    }

    /**
     * Modifier présence (admin seulement)
     */
    public function update(Request $request, Presence $presence)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $request->validate([
            'etat' => 'sometimes|in:present,absent,retard',
            'heure_entree' => 'nullable|date',
            'heure_sortie' => 'nullable|date'
        ]);

        $presence->update($request->only([
            'etat',
            'heure_entree',
            'heure_sortie'
        ]));

        return response()->json([
            'message' => 'Présence mise à jour',
            'presence' => $presence
        ]);
    }

    /**
     * Supprimer (admin)
     */
    public function destroy(Presence $presence, Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $presence->delete();

        return response()->json([
            'message' => 'Présence supprimée'
        ]);
    }
}
