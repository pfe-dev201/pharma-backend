<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use App\Models\Entree;
use App\Models\Sortie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class StockController extends Controller
{
    public function getOptions()
    {
        $produits = Entree::distinct()->pluck('designation')->prepend('CHOISIR UN PRODUIT')->all();
        $categories = Entree::distinct()->pluck('categorie')->prepend('CHOISIR UNE CATEGORIE')->all();

        return response()->json(["produits" => $produits, "categories" => $categories]);
    }


    // data situation globale
    public function getDataSituationGlobale()
    {
        //configuration
        $procheTerminee = Configuration::find(1)->periode_proche_terminee;

        //entrees
        $entrees = Entree::select('designation', 'categorie', DB::raw('SUM(quantite) as quantite'))
            ->groupBy('designation', 'categorie')
            ->get();

        //sorties
        $sorties = Sortie::select('designation', 'categorie', DB::raw('SUM(quantite) as quantite'))
            ->groupBy('designation', 'categorie')
            ->get();

        //calcul du stock
        foreach ($entrees as $entree) {
            foreach ($sorties as $sortie) {
                if ($entree->designation == $sortie->designation && $entree->categorie == $sortie->categorie) {
                    $entree->quantite -= $sortie->quantite;
                }
            }
        }

        //resultat
        $result = [];
        foreach ($entrees as $entree) {
            if ($entree->quantite != 0) {
                $element = [
                    "categorie" => $entree->categorie,
                    "designation" => $entree->designation,
                    "stock" => $entree->quantite
                ];

                if ($entree->quantite > $procheTerminee) {
                    $element["etatQuantite"] = "OK";
                } elseif ($entree->quantite > 0 && $entree->quantite <= $procheTerminee) {
                    $element["etatQuantite"] = "proche terminé";
                } else {
                    $element["etatQuantite"] = "terminé";
                }
                $result[] = $element;
            }
        }
        return $result;
    }

    // data designation
    public function getDataDesignation($produit)
    {
        //configuration
        $procheTerminee = Configuration::find(1)->periode_proche_terminee;
        $prochePerimee = Configuration::find(1)->periode_proche_perimee;

        // DataFrame stock
        $entrees = Entree::select('date', 'categorie', 'designation', 'peremption', 'quantite')->get();

        // DataFrame livraison
        $sorties = Sortie::select('date', 'categorie', 'designation', 'quantite')->get();

        // Signe - pour les quantités sorties
        foreach ($sorties as $sortie) {
            $sortie->quantite = -$sortie->quantite;
        }

        // Concaténation des entrées et sorties dans une seule DataFrame
        $data = $entrees->concat($sorties)->filter(function ($item) use ($produit) {
            return $item->designation === $produit;
        });

        // Trier la DataFrame par date
        $data = $data->sortBy("date")->values();

        // Calcul du stock
        $stockQuantite = 0;
        foreach ($data as $item) {
            $item["entreeSortie"] = $item->quantite;
            $stockQuantite += $item->quantite;
            $item["stock"] = $stockQuantite;
        }

        // Etat péremption
        foreach ($data as $element) {
            if ($element->peremption) {
                $date1 = Carbon::now();
                $date2 = Carbon::createFromFormat('Y-m-d', $element->peremption);
    
                $diffInDays = $date1->diffInDays($date2);
          
                if ($diffInDays > $prochePerimee) {
                    $element["etatPeremption"] = "OK";
                } elseif ($diffInDays > 0 && $diffInDays <= $prochePerimee) {
                    $element["etatPeremption"] = "proche périmé";
                } else {
                    $element["etatPeremption"] = "périmé";
                }
            } else {
                $element["etatPeremption"] = "";
            }
        }

        // Etat stock
        foreach ($data as &$element) {
            if ($element->stock > $procheTerminee) {
                $element["etatQuantite"] = "OK";
            } elseif ($element->stock > 0 && $element->stock <= $procheTerminee) {
                $element["etatQuantite"] = "proche terminé";
            } else {
                $element["etatQuantite"] = "terminé";
            }
        }

        return $data;
    }

    // data categorie
    public function getDataCategorie($categorie)
    {
        //configuration
        $procheTerminee = Configuration::find(1)->periode_proche_terminee;

        // Query pour le stock
        $entrees = Entree::select('designation', 'categorie', DB::raw('SUM(quantite) as total_quantite'))
            ->groupBy('designation', 'categorie');

        // Query pour les livraisons
        $sorties = Sortie::select('designation', 'categorie', DB::raw('SUM(quantite) as total_quantite'))
            ->groupBy('designation', 'categorie');

        // Jointure des résultats
        $result = DB::table(DB::raw("({$entrees->toSql()}) as entrees"))
        ->leftJoin(DB::raw("({$sorties->toSql()}) as sorties"), function ($join) {
            $join->on('entrees.designation', '=', 'sorties.designation')
                ->on('entrees.categorie', '=', 'sorties.categorie');
        })
        ->select('entrees.designation', 'entrees.categorie', DB::raw('entrees.total_quantite - IFNULL(sorties.total_quantite, 0) as stock_final'))
        ->where('entrees.categorie', '=', $categorie)
        ->get();

        // Transformation des résultats en liste
        $resultList = [];
        foreach ($result as $element) {
            if ($element->stock_final > $procheTerminee) {
                $etatStock = "OK";
            } elseif ($element->stock_final > 0 && $element->stock_final <= $procheTerminee) {
                $etatStock = "proche terminé";
            } else {
                $etatStock = "terminé";
            }
            $resultList[] = [
                'designation' => $element->designation,
                'categorie' => $element->categorie,
                'stock' => $element->stock_final,
                'etatQuantite' => $etatStock,
            ];
        }
        return $resultList;
    }

    public function getData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "chercherPar" => Rule::in(["SITUATION GLOBALE", "DESIGNATION", "CATEGORIE"]),
            "dateDuJour" => ["date"],
            "produit" => Rule::in(Entree::distinct()->pluck('designation')->prepend('CHOISIR UN PRODUIT')),
            "categorie" => Rule::in(Entree::distinct()->pluck('categorie')->prepend('CHOISIR UNE CATEGORIE'))
        ]);

        if ($validator->fails()) {
            return response()->json(["status" => "error", "error" => $validator->errors()]);
        }

        if ($request->chercherPar === "SITUATION GLOBALE") {
            return response()->json(["status" => "success", "stocks" => $this->getDataSituationGlobale()]);
        } else if ($request->chercherPar === "DESIGNATION") {
            return response()->json(["status" => "success", "stocks" => $this->getDataDesignation($request->produit)]);
        } else if ($request->chercherPar === "CATEGORIE") {
            return response()->json(["status" => "success", "stocks" => $this->getDataCategorie($request->categorie)]);
        }
    }
}
