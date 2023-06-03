<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::where("status", "!=", "SUPER")->get();
        foreach ($users as $user) {
            $user->setAttribute("typeRole", $user->getRole());
        }
        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $messages = [
            "required" => "le champ :attribute est obligatoire.",
            "string" => "le champ :attribute doit être une chaine de caractères.",
            "in" => "la valeur du champ :attribute n'st pas valide.",
            "email" => "l'email choisi n'est pas valide.",
            "unique" => "l'email choisi est déjà pris"
          
        ];

        $validator = Validator::make($request->all(),[
            "nom" => ["required", "string"],
            "prenom" => ["required", "string"],
            "email" => ["required", "email", "unique:users,email"],
            "status" => ["required", Rule::in(["ADMIN", "UTILISATEUR"])],
            "role" => ["required", Rule::in(["ECRIRE-LIRE", "ECRIRE", "LIRE"])],
        ], $messages);

        if ($validator->fails()) {
            return response()->json(["status" => "error", "error" => $validator->errors()]);
        }

        if ($request->role === "ECRIRE-LIRE") {
            $role_id = 1;
        } else if ($request->role === "ECRIRE") {
            $role_id = 2;
        } else if ($request->role === "LIRE") {
            $role_id = 3;
        }

        $user = new User();

        $user->nom= $request->nom;
        $user->prenom = $request->prenom;
        $user->email = $request->email;
        $user->image_profile = "default-profile.jpg";
        $user->password = Hash::make('0000');
        $user->status = $request->status;
        $user->role_id = $role_id;

        $user->save();

        $users = User::where("status", "!=", "SUPER")->get();
        foreach ($users as $user) {
            $user->setAttribute("typeRole", $user->getRole());
        }

        return response()->json(["status" => "success", "message" => "l'utilisateur a bien été ajouté", "users" => $users]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $messages = [
            "required" => "le champ :attribute est obligatoire.",
            "string" => "le champ :attribute doit être une chaine de caractères.",
            "in" => "la valeur du champ :attribute n'st pas valide.",
            "email" => "l'email choisi n'est pas valide.",
        ];

        $validator = Validator::make($request->all(),[
            "nom" => ["required", "string"],
            "prenom" => ["required", "string"],
            "email" => ["required", "email"],
            "status" => ["required", Rule::in(["ADMIN", "UTILISATEUR"])],
            "role" => ["required", Rule::in(["ECRIRE-LIRE", "ECRIRE", "LIRE"])],
        ], $messages);

        if ($validator->fails()) {
            return response()->json(["status" => "error", "error" => $validator->errors()]);
        }

        if (User::where('email', $request->email)->exists() && $user->email !== $request->email) {
            return response()->json(["status" => "error", "error" => ["email" => ["l'email choisi est déjà pris"]]]);
        }

        if ($request->role === "ECRIRE-LIRE") {
            $role_id = 1;
        } else if ($request->role === "ECRIRE") {
            $role_id = 2;
        } else if ($request->role === "LIRE") {
            $role_id = 3;
        }

        $user->nom= $request->nom;
        $user->prenom = $request->prenom;
        $user->email = $request->email;
        $user->status = $request->status;
        $user->role_id = $role_id;

        $user->save();

        $users = User::where("status", "!=", "SUPER")->get();
        foreach ($users as $user) {
            $user->setAttribute("typeRole", $user->getRole());
        }

        return response()->json(["status" => "success", "message" => "l'utilisateur a bien été modifié", "users" => $users]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        $users = User::where("status", "!=", "SUPER")->get();
        foreach ($users as $user) {
            $user->setAttribute("typeRole", $user->getRole());
        }

        return response()->json(["status" => "success", "message" => "l'utilisateur a bien été supprimé", "users" => $users]);
    }

    // trie
    public function setTrie(Request $request)
    {
        $messages = [
            "array" => ":attribute doit être sous forme d'un tableau.",
            "in" => "la valeur du champ :attribute n'est pas valide."
        ];

        $attributs = [
            "trierPar" => '"trier par"',
            "typeTrie" => '"type de trie"',
        ];

        $validator = Validator::make($request->all(), [
            "users" => ["array"],
            "trierPar" => Rule::in(["NOM", "PRENOM", "EMAIL", "STATUS", "ROLE"]),
            "typeTrie" => Rule::in(["ASC", "DESC"])
        ], $messages, $attributs);

        if ($validator->fails()) {
            return response()->json(["status" => "error", "error" => $validator->errors()]);
        }

        if ($request->has("users")) {
            $collection = collect($request->users);
        } else {
            $users_collection = User::where("status", "!=", "SUPER")->get();
            foreach ($users_collection as $user) {
                $user->setAttribute("typeRole", $user->getRole());
            }
            $collection = $users_collection;
        }

        $users = $collection->sortBy(strtolower($request->trierPar))->values()->all();
        
        return response()->json(["status" => "success", "message" => "les utilisateurs ont bien été triés", "users" => $users]);
    }

    //filtre
    public function setFiltre(Request $request)
    {
        $messages = [
            "array" => ":attribute doit être sous forme d'un tableau.",
            "in" => "la valeur du champ :attribute n'est pas valide.",
            "string" => "le champ :attribute doit être une chaine de caractères."
        ];

        $attributs = [
            "filtrerPar" => '"filtrer par"',
            "typeFiltre" => '"type de filtre"',
            "egaleA" => '"égale à"',
            "commencePar" => '"commence par"',
            "terminePar" => '"termine par"',
            "statusEgaleA" => '"status égale à"',
            "roleEgaleA" => '"role égale à"'
        ];

        $validator = Validator::make($request->all(), [
            "users" => ["array"],
            "filtrerPar" => Rule::in(["NOM", "PRENOM", "EMAIL", "STATUS", "ROLE"]),
            "egaleA" => ['nullable', "string"],
            "commencePar" => ["nullable", "string"],
            "terminePar" => ["nullable", "string"],
            "statusEgaleA" => ["nullable", "string"],
            "roleEgaleA" => ["nullable", "string"],
        ], $messages, $attributs);

        if ($validator->fails()) {
            return response()->json(["status" => "error", "error" => $validator->errors()]);
        }

        if ($request->filtrerPar === "ROLE" && is_null($request->roleEgaleA)) {
            return response()->json(["status" => "error", "error" => ["roleEgaleA" => ['le champ "role égale à" est obligatoire']]]);
        } else if ($request->filtrerPar === "STATUS" && is_null($request->statusEgaleA)) {
            return response()->json(["status" => "error", "error" => ["statusEgaleA" => ['le champ "status égale à" est obligatoire']]]);
        }

        if ($request->has("users")) {
            $collection = collect($request->users);
        } else {
            $users_collection = User::where("status", "!=", "SUPER")->get();
            foreach ($users_collection as $user_collection) {
                $user_collection->setAttribute("typeRole", $user_collection->getRole());
            }
            $collection = $users_collection;
        }

        if ($request->typeFiltre === "EGALE A") {
            $users = $collection->where(strtolower($request->filtrerPar), $request->egaleA)->all();
            
        } else if ($request->typeFiltre === "COMMENCE PAR") {
            $users = $collection->filter(function ($item) use ($request) {
                return str_starts_with($item[strtolower($request->filtrerPar)], $request->commencePar);
            })->all();

        } else if ($request->typeFiltre === "TERMINE PAR") {
            $users = $collection->filter(function ($item) use ($request) {
                return str_ends_with($item[strtolower($request->filtrerPar)], $request->terminePar);
            })->all();
            
        } else if ($request->typeFiltre === "STATUS EGALE A") {
            $users = $collection->where("status", $request->statusEgaleA)->all();
            
        } else if ($request->typeFiltre === "ROLE EGALE A") {
            if ($request->roleEgaleA === "ECRIRE") {
                $users = $collection->where("role_id", 2)->all();
            } else if ($request->roleEgaleA === "LIRE") {
                $users = $collection->where("role_id", 3)->all();
            }
            
        }

        return response()->json(["status" => "success", "message" => "les utilisateurs ont bien été filtrés", "users" => $users]);
    }
}
