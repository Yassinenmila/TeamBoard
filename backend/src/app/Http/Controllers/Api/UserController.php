<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Liste des utilisateurs (admin uniquement)
     */
    public function index(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        return response()->json(User::all());
    }

    /**
     * Créer un utilisateur
     */
    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string',
            'role' => 'required|in:admin,responsable,membre'
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);


        return response()->json([
            'message' => 'Utilisateur créé avec succès',
            'user' => $user
        ], 201);
    }

    /**
     * Afficher un utilisateur
     */
    public function show(User $user, Request $request)
    {
        if ($request->user()->role !== 'admin' && $request->user()->id !== $user->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $user->load([
            'tachesCrees',
            'tachesAssignes',
            'annonces',
            'reunionsCreees',
            'reunions'
        ]);

        return response()->json([
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'role' => $user->role,

            // COUNTS
            'taches_creees_count' => $user->tachesCrees->count(),
            'annonces_count' => $user->annonces->count(),
            'reunions_creees_count' => $user->reunionsCreees->count(),
            'taches_assignees_count' => $user->tachesAssignes->count(),
            'reunions_invitations_count' => $user->reunions->count(),

            // LISTES
            'creations' => $user->tachesCrees->map(fn($t) => [
                'id' => $t->id,
                'title' => $t->titre,
                'date' => $t->created_at->format('d M'),
                'type' => 'tache'
            ]),

            'assignations' => $user->tachesAssignes->map(fn($t) => [
                'id' => $t->id,
                'title' => $t->titre,
                'date' => $t->date_limite,
                'type' => 'tache'
            ]),
        ]);
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function update(Request $request, User $user)
    {
        $currentUser = $request->user();

        // Admin ou lui-même
        if ($currentUser->role !== 'admin' && $currentUser->id !== $user->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $request->validate([
            'first_name' => 'sometimes|string',
            'last_name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'role' => 'sometimes|in:admin,responsable,membre'
        ]);

        $data = $request->only(['first_name', 'last_name', 'email', 'role']);

        // Hash password si fourni
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Seul admin peut changer le rôle
        if ($currentUser->role !== 'admin') {
            unset($data['role']);
        }

        $user->update($data);

        return response()->json([
            'message' => 'Utilisateur mis à jour',
            'user' => $user
        ]);
    }

    /**
     * Supprimer un utilisateur
     */
    public function destroy(User $user, Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        // éviter suppression de soi-même (optionnel mais recommandé)
        if ($request->user()->id === $user->id) {
            return response()->json([
                'message' => 'Vous ne pouvez pas supprimer votre propre compte'
            ], 400);
        }

        $user->delete();

        return response()->json([
            'message' => 'Utilisateur supprimé avec succès'
        ]);
    }
}
