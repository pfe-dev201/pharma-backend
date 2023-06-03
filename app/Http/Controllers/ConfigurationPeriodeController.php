<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConfigurationPeriodeController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show()
    {
        $configuration = Configuration::find(1);
        return response()->json($configuration);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $messages = [
            "required" => "le champ :attribute est obligatoire.",
            "integer" => "le champ :attribute un nombre entier."
        ];

        $attributs = [
            "prochePerimee" => '"période proche périmée"',
            "procheTerminee" => '"période proche términée"'
        ];

        $validator = Validator::make($request->all(),[
            "prochePerimee" => ["required", "integer"],
            "procheTerminee" => ["required", "integer"]
        ], $messages, $attributs);

        if ($validator->fails()) {
            return response()->json(["status" => "error", "error" => $validator->errors()]);
        }

        $configuration = Configuration::find(1);

        $configuration->periode_proche_perimee= $request->prochePerimee;
        $configuration->periode_proche_perimee= $request->prochePerimee;

        $configuration->save();

        return response()->json(["status" => "success", "message" => "les périodes ont bien été modifiés", "configuration" => $configuration]);
    
    }
}
