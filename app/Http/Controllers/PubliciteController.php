<?php

namespace App\Http\Controllers;

use App\Models\Publicite;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class PubliciteController extends Controller
{
    public function index() {
       // Logic to retrieve and return all publicities
        $publicites = Publicite::all(); // Example of retrieving all publicities
        return response()->json([
            'data' => $publicites,
            'status' => 'success',
            'message' => 'List of publicities',
            // Here you would typically return the publicities from the database
        ]); 
    }
    
    public function indexforusers() {
        // Logic to retrieve and return all publicities
        $publicites = Publicite::where('is_active', true)->get(); // Example of retrieving all publicities
        return response()->json([
            'data' => $publicites,
            'status' => 'success',
            'message' => 'List of publicities',
            // Here you would typically return the publicities from the database
        ]);
    }
    public function show($id) {
        // Logic to retrieve and return a specific publicity by ID
        $publicite = Publicite::find($id);
        if (!$publicite) {
            return response()->json([
                'status' => 'error',
                'message' => 'Publicity not found',
            ], 404);
        }
        return response()->json([
            'data' => $publicite,
            'status' => 'success',
            'message' => 'Publicity details',
        ]);
    }
    public function store(Request $request) {
        // Logic to create a new publicity
        $validator = Validator::make($request->all(), [
            'libelle' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 422);
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension();
            $newFileName = $request['libelle']+time() . '.' . $extension;
            $image->move(public_path('images/publicites'), $newFileName);
            $user['image'] = $newFileName;
        }

        $publicite = Publicite::create($request->toArray());

        return response()->json([
            'data' => $publicite,
            'status' => 'success',
            'message' => 'Publicity created successfully',
        ]);
    }

    /* public function desactivePublicite() {
        // Logic to retrieve and return all publicities that are not active
        $publicites = Publicite::where('is_active', true)->get(); // Example of retrieving all non-active publicities

        if ($publicites->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No active publicities found',
            ], 404);
        }

        //En Laravel 10, la comparaison de deux dates de type datetime peut être réalisée en utilisant la classe Carbon, qui est intégrée dans Laravel pour la manipulation des dates. Vous pouvez comparer les dates en utilisant des méthodes telles que eq(), ne(), gt(), gte(), lt(), et lte() pour vérifier l'égalité, l'inégalité, la supériorité, la supériorité ou l'égalité, l'infériorité, et l'infériorité ou l'égalité respectivement. 

        foreach ($publicites as $key => $value) {
            if (Carbon::parse($value->date_fin)->lte(Carbon::parse(Carbon::now()))) {
                $value->is_active = false; // Set the publicity as inactive if the start date is equal to the end date
                $value->update();
            }
        }

         // Here you would typically return the publicities from the database
        return response()->json([
            'data' => $publicites,
            'status' => 'success',
            'message' => 'List of active publicities',
            // Here you would typically return the publicities from the database
        ]);
    } */

    public function update(Request $request, $id) {
        // Logic to update an existing publicity
        $publicite = Publicite::find($id);
        if (!$publicite) {
            return response()->json([
                'status' => 'error',
                'message' => 'Publicity not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'libelle' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'date_debut' => 'sometimes|required|date',
            'date_fin' => 'sometimes|required|date|after_or_equal:date_debut',
            'is_active' => 'sometimes|required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 422);
        }

        $publicite->update($request->all());

        return response()->json([
            'data' => $publicite,
            'status' => 'success',
            'message' => 'Publicity updated successfully',
        ]);
    }
    public function destroy($id) {
        // Logic to delete a publicity
        $publicite = Publicite::find($id);
        if (!$publicite) {
            return response()->json([
                'status' => 'error',
                'message' => 'Publicity not found',
            ], 404);
        }

        // Delete the publicity image if it exists
        File::delete('images/publicites/' . $publicite->image); // Delete the image file if it exists
        $publicite->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Publicity deleted successfully',
        ]);
    }
    public function toggleStatus($id) {
        // Logic to toggle the active status of a publicity
        $publicite = Publicite::find($id);
        if (!$publicite) {
            return response()->json([
                'status' => 'error',
                'message' => 'Publicity not found',
            ], 404);
        }

        $publicite->is_active = !$publicite->is_active;
        $publicite->save();

        return response()->json([
            'data' => $publicite,
            'status' => 'success',
            'message' => 'Publicity status toggled successfully',
        ]);
    }
}
