<?php

namespace App\Http\Controllers;

use App\Models\Entree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EntreeMedicamentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $entrees = Entree::all();
        return response()->json($entrees);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "date" => ["required", "date"],
            "peremption" => ["required", "date"],
            "categorie" => ["required", "string"],
            "designation" => ["required", "string"],
            "conditionnement" => ["required", "string"],
            "quantite" => ["required", "integer", "min:0"]
        ]);

        if ($validator->fails()) {
            return response()->json(["status" => "error", "error" => $validator->errors()]);
        }

        $entree = new Entree();

        $entree->user_id = 1;
        $entree->date = $request->date;
        $entree->peremption = $request->peremption;
        $entree->categorie = $request->categorie;
        $entree->designation = $request->designation;
        $entree->conditionnement = $request->conditionnement;
        $entree->quantite = $request->quantite;

        $entree->save();

        return response()->json(["status" => "success", "message" => "le médicament a bien été ajouté", "medicaments" => Entree::all()]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Entree $entree)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Entree $entree)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Entree $entree)
    {
        //
    }
}
