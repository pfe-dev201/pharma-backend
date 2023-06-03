<?php

namespace App\Http\Controllers;

use App\Models\Entree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
        $messages = [
            "required" => "le champ :attribute est obligatoire.",
            "date" => "le champ :attribute doit être une date valide.",
            "string" => "le champ :attribute doit être une chaine de caractères.",
            "integer" => "le champ :attribute un nombre entier.",
            "min" => [
                "numeric" => "le champ :attribute doit être au moins égale à :min."
            ]
        ];

        $attributs = [
            "peremption" => "péremption",
            "categorie" => "catégorie",
            "designation" => "désignation",
            "quantite" => "quantité"
        ];

        $validator = Validator::make($request->all(),[
            "user_id" => ["required", "integer"],
            "date" => ["required", "date"],
            "peremption" => ["required", "date"],
            "categorie" => ["required", "string"],
            "designation" => ["required", "string"],
            "conditionnement" => ["required", "string"],
            "quantite" => ["required", "integer", "min:1"]
        ], $messages, $attributs);

        if ($validator->fails()) {
            return response()->json(["status" => "error", "error" => $validator->errors()]);
        }

        $entree = new Entree();

        $entree->user_id = $request->user_id;
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
        $messages = [
            "required" => "le champ :attribute est obligatoire.",
            "date" => "le champ :attribute doit être une date valide.",
            "string" => "le champ :attribute doit être une chaine de caractères.",
            "integer" => "le champ :attribute un nombre entier.",
            "min" => [
                "numeric" => "le champ :attribute doit être au moins égale à :min."
            ]
        ];

        $attributs = [
            "peremption" => "péremption",
            "categorie" => "catégorie",
            "designation" => "désignation",
            "quantite" => "quantité"
        ];

        $validator = Validator::make($request->all(),[
            "user_id" => ["required", "integer"],
            "date" => ["required", "date"],
            "peremption" => ["required", "date"],
            "categorie" => ["required", "string"],
            "designation" => ["required", "string"],
            "conditionnement" => ["required", "string"],
            "quantite" => ["required", "integer", "min:1"]
        ], $messages, $attributs);

        if ($validator->fails()) {
            return response()->json(["status" => "error", "error" => $validator->errors()]);
        }

        $entree->user_id = $request->user_id;
        $entree->date = $request->date;
        $entree->peremption = $request->peremption;
        $entree->categorie = $request->categorie;
        $entree->designation = $request->designation;
        $entree->conditionnement = $request->conditionnement;
        $entree->quantite = $request->quantite;

        $entree->save();

        return response()->json(["status" => "success", "message" => "le médicament a bien été modifié", "medicaments" => Entree::all()]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Entree $entree)
    {
        $entree->delete();
        return response()->json(["status" => "success", "message" => "le medicament a bien été supprimé", "medicaments" => Entree::all()]);
    }

    // trie
    public function setTrie(Request $request)
    {
        $messages = [
            "array" => ":attribute doit être sous forme d'un tableau.",
            "in" => "la valeur du champ :attribute n'st pas valide."
        ];

        $attributs = [
            "trierPar" => '"trier par"',
            "typeTrie" => '"type de trie"',
        ];

        $validator = Validator::make($request->all(), [
            "medicaments" => ["array"],
            "trierPar" => Rule::in(["DATE", "PEREMPTION", "CATEGORIE", "DESIGNATION", "CONDITIONNEMENT", "QUANTITE"]),
            "typeTrie" => Rule::in(["ASC", "DESC"])
        ], $messages, $attributs);

        if ($validator->fails()) {
            return response()->json(["status" => "error", "error" => $validator->errors()]);
        }

        if ($request->has("medicaments")) {
            $collection = collect($request->medicaments);
        } else {
            $collection = Entree::all();
        }

        $medicaments = $collection->sortBy(strtolower($request->trierPar))->values()->all();
        
        return response()->json(["status" => "success", "message" => "les médicaments ont bien été triés", "medicaments" => $medicaments]);
    }

    //filtre
    public function setFiltre(Request $request)
    {
        $messages = [
            "array" => ":attribute doit être sous forme d'un tableau.",
            "in" => "la valeur du champ :attribute n'st pas valide.",
            "date" => "le champ :attribute doit être une date valide.",
            "string" => "le champ :attribute doit être une chaine de caractères.",
            "integer" => "le champ :attribute un nombre entier."
        ];

        $attributs = [
            "filtrerPar" => '"filtrer par"',
            "typeFiltre" => '"type de filtre"',
            "dateSuperieurA" => '"date supérieur à"',
            "dateInferieurA" => '"date inférieur à"',
            "dateEgaleA" => '"date égale à"',
            "egaleA" => '"égale à"',
            "commencePar" => '"commence par"',
            "terminePar" => '"termine par"',
            "inferieurA" => '"inférieur à"',
            "superieurA" => '"supérieur à"',
        ];

        $validator = Validator::make($request->all(), [
            "medicaments" => ["array"],
            "filtrerPar" => Rule::in(["DATE", "PEREMPTION", "CATEGORIE", "DESIGNATION", "CONDITIONNEMENT", "QUANTITE"]),
            "typeFiltre" => Rule::in(["DATE EGALE A", "DATE INFERIEUR A", "DATE SUPERIEUR A", "EGALE A", "COMMENCE PAR", "TERMINE PAR", "INFERIEUR A", "SUPERIEUR A"]),
            "dateSuperieurA" => ["nullable", "date"],
            "dateInferieurA" => ["nullable", "date"],
            "dateEgaleA" => ["nullable", "date"],
            "egaleA" => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if (!is_null($value) && !is_numeric($value) && !is_string($value)) {
                        $fail('Le champ '.$attribute.' doit être un nombre, une chaîne de caractères ou null.');
                    }
                },
            ],
            "commencePar" => ["nullable", "string"],
            "terminePar" => ["nullable", "string"],
            "inferieurA" => ["nullable", "integer"],
            "superieurA" => ["nullable", "integer"]
        ], $messages, $attributs);

        if ($validator->fails()) {
            return response()->json(["status" => "error", "error" => $validator->errors()]);
        }

        if ($request->has("medicaments")) {
            $collection = collect($request->medicaments);
        } else {
            $collection = Entree::all();
        }

        if ($request->typeFiltre === "DATE EGALE A") {
            if ($request->filtrerPar === "DATE") {
                $medicaments = $collection->where("date", $request->dateEgaleA)->all();
            } else if ($request->filtrerPar === "PEREMPTION") {
                $medicaments = $collection->where("peremption", $request->dateEgaleA)->all();
            }

        } else if ($request->typeFiltre === "DATE INFERIEUR A") {
            if ($request->filtrerPar === "DATE") {
                $medicaments = $collection->where("date", "<", $request->dateInferieurA)->all();
            } else if ($request->filtrerPar === "PEREMPTION") {
                $medicaments = $collection->where("peremption", "<", $request->dateInferieurA)->all();
            }

        } else if ($request->typeFiltre === "DATE SUPERIEUR A") {
            if ($request->filtrerPar === "DATE") {
                $medicaments = $collection->where("date", ">", $request->dateSuperieurA)->all();
            } else if ($request->filtrerPar === "PEREMPTION") {
                $medicaments = $collection->where("peremption", ">", $request->dateSuperieurA)->all();
            }

        } else if ($request->typeFiltre === "EGALE A") {
            $medicaments = $collection->where(strtolower($request->filtrerPar), $request->egaleA)->all();
            
        } else if ($request->typeFiltre === "COMMENCE PAR") {
            $medicaments = $collection->filter(function ($item) use ($request) {
                return str_starts_with($item[strtolower($request->filtrerPar)], $request->commencePar);
            })->all();

        } else if ($request->typeFiltre === "TERMINE PAR") {
            $medicaments = $collection->filter(function ($item) use ($request) {
                return str_ends_with($item[strtolower($request->filtrerPar)], $request->terminePar);
            })->all();
            
        } else if ($request->typeFiltre === "INFERIEUR A") {
            $medicaments = $collection->where("quantite", "<", $request->inferieurA)->all();

        } else if ($request->typeFiltre === "SUPERIEUR A") {
            $medicaments = $collection->where("quantite", ">", $request->superieurA)->all();

        }

        return response()->json(["status" => "success", "message" => "les médicaments ont bien été filtrés", "medicaments" => $medicaments]);
    }
}
