<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tache;

class TacheController extends Controller
{

    public function index(Request $request){
        $user = $request->user();

        if ($user->role === 'admin') {
            $taches = Tache::with(['createur','utilisateurs','livrables','commentaires'])->get();
        }

        elseif ($user->role === 'responsable') {
            $taches = Tache::with(['createur','utilisateurs','livrables','commentaires'])
                ->whereHas('createur', function($q){
                    $q->where('role','responsable');
                })->get();
        }

        elseif ($user->role === 'membre') {
            $taches = $user->tachesAssignes()->with(['createur','utilisateurs','livrables','commentaires'])->get();
        }

        else {
            return response()->json([
                'message'=>'Role non reconnu'
            ],403);
        }

        return response()->json($taches);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre'=>'required|string',
            'description'=>'required|string',
            'status'=>'required|in:en cours,terminée,en attente',
            'date_limite'=>'required|date',
        ]);

        $tache = Tache::create(array_merge($validated, ['created_by'=>$request->user()->id]));

        return response()->json([
            'message' => 'Tâche créée avec succès',
            'tache' => $tache
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tache = Tache::with(['createur','utilisateurs','livrables','commentaires'])->findOrFail($id);
        return response()->json($tache);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $tache = Tache::findOrFail($id);

        // Sécurité : seul le créateur ou l'admin peut modifier
        if ($request->user()->id !== $tache->created_by && $request->user()->role !== 'admin') {
            return response()->json(['message'=>'Non autorisé'], 403);
        }

        $validated = $request->validate([
            'titre' => 'sometimes|string',
            'description' => 'sometimes|string',
            'priorite' => 'sometimes|string',
            'date_limite' => 'sometimes|date',
            'statut' => 'sometimes|string'
        ]);

        $tache->update($validated);

        return response()->json([
            'message' => 'Tâche mise à jour',
            'tache' => $tache
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request)
    {

        $tache = Tache::findOrFail($id);

        // Sécurité : seul le créateur ou l'admin peut supprimer
        if ($request->user()->id !== $tache->created_by && $request->user()->role !== 'admin') {
            return response()->json(['message'=>'Non autorisé'], 403);
        }

        $tache->delete();

        return response()->json(['message'=>'Tâche supprimée avec succès']);
    }

    public function assigner(Request $request, $id){
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        $tache = Tache::findOrFail($id);
        $tache->utilisateurs()->syncWithoutDetaching($request->user_ids);

        // Notifier chaque utilisateur assigné
        foreach ($request->user_ids as $user_id) {

            $tache->notifications()->create([
                'user_id' => $user_id,
                'message' => "Vous avez été assigné à la tâche : {$tache->titre}",
                'lu' => false
            ]);
        }

        return response()->json([
            'message'=>'Tâche assignée avec succès',
            'tache'=>$tache->load('utilisateurs')
        ]);
    }
}
