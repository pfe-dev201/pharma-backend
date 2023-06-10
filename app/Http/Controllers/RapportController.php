<?php

namespace App\Http\Controllers;

use App\Models\Entree;
use App\Models\Sortie;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RapportController extends Controller
{
    public function index()
    {
        $rapports = Entree::select('designation')->distinct()->get();
        return response()->json($rapports);
    }

    public function get_mid_date(Request $request)
    {
        if ($request->has("designation")) {
            $collection = ["designation" => $request->designation, "date" => $request->date];
            $medicaments = $collection;
            // $medicaments[] = $collection['date'];
        } else {
            $collection = Entree::Distinct('designation')->get();
            $medicaments = $collection;
        }

        return response()->json($medicaments);
    }

    public function get_entree_sortie($mid = null,$year = null)
    {

        $midRapport = [];
        $midSortie = [];
        $data = [];
        if ($mid) {

            $rapports = Entree::where('designation', 'like', $mid)->get();
            $sorties = Sortie::where('designation', 'like', $mid)->get();
            foreach ($rapports as $rapp) {
                $carbonDate = Carbon::createFromFormat('Y-m-d', $rapp->date);
                $rapp->quantite;

                $midRapport['type'] = "entree";
                $midRapport['designation'] = $rapp->designation;
                $midRapport['quantite'] = $rapp->quantite;
                $midRapport['month'] = $carbonDate->month;
                $midRapport['year'] = $carbonDate->year;
                $data[] = $midRapport;
            }

            foreach ($sorties as $sort) {
                $carbonDate = Carbon::createFromFormat('Y-m-d', $sort->date);
                $sort->quantite;

                $midSortie['type'] = "sortie";
                $midSortie['designation'] = $sort->designation;
                $midSortie['quantite'] = $sort->quantite;
                $midSortie['month'] = $carbonDate->month;
                $midSortie['year'] = $carbonDate->year;
                $data[] = $midSortie;
            }
        }
        return response()->json($data);
    }
}
