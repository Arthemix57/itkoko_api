<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ProduitController extends Controller
{
    public function index()
    {
        // Logic to retrieve and return all products
        $produits = Produit::all(); // Example of retrieving all products
        return response()->json([
            'data' => $produits,
            'status' => 'success',
            'message' => 'List of products',
            // Here you would typically return the products from the database
        ]);
    }

    public function indexforusers()
    {
        // Logic to retrieve and return all products
        $produits = Produit::where('is_active', true)->get(); // Example of retrieving all products
        return response()->json([
            'data' => $produits,
            'status' => 'success',
            'message' => 'List of products',
            // Here you would typically return the products from the database
        ]);
    }

    public function show($id)
    {
        // Logic to retrieve and return a specific product by ID
        $produit = Produit::find($id);
        if (!$produit) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ], 404);
        }
        return response()->json([
            'data' => $produit,
            'status' => 'success',
            'message' => 'Product details',
        ]);
    }

    public function store(Request $request)
    {
        // Logic to create a new product
        $validator = Validator::make($request->all(), [
            'libelle' => 'required|string|max:255',
            'description' => 'nullable|string',
            'pu' => 'required|numeric|min:0',
            'image' => 'required|string|max:255', // Assuming image is a URL or path
        ]);
        // Validate the request data
        // You can add more validation rules as needed
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        // Create the product using the validated data
        $request['is_active'] = $request->has('is_active') ? $request->is_active : true; // Default to true if not provided
        $request['pu'] = $request->pu ? $request->pu : 0.00; // Default to 0.00 if not provided
        $request['qte'] = $request->qte ? $request->qte : 0; // Default to 0 if not provided
        $request['libelle'] = ucwords(Str::lower($request['libelle'])); // Normalize the product name
        $request['description'] = $request->description ? $request->description : ''; // Default to empty string if not provided

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension();
            $newFileName = $request['libelle']+time() . '.' . $extension;
            $image->move(public_path('images/produits'), $newFileName);
            $user['image'] = $newFileName;
        }

        $produit = Produit::create($request->toArray());
        return response()->json([
            'data' => $produit,
            'status' => 'success',
            'message' => 'Product created successfully',
        ], 201);
    }

    public function update(Request $request, $id)
    {
        // Logic to update an existing product
        $produit = Produit::find($id);
        if (!$produit) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ], 404);
        }
        $validator = Validator::make($request->all(), [
            'libelle' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'pu' => 'sometimes|required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        // Update the product with the validated data
        $request['is_active'] = $request->has('is_active') ? $request->is_active : true; // Default to true if not provided
        $request['pu'] = $request->pu ? $request->pu : 0.00; // Default to 0.00 if not provided
        $request['qte'] = $request->qte ? $request->qte : 0; // Default to 0 if not provided
        $request['libelle'] = ucwords(Str::lower($request['libelle'])); // Normalize the product name
        $request['description'] = $request->description ? $request->description : ''; // Default to empty string if not provided

        // Handle image upload if provided
        if ($request->hasFile('image')) {
            File::delete('images/produits' . $produit->image);
            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension();
            $newFileName = $request['libelle'] . time() . '.' . $extension;
            $image->move(public_path('images/produits'), $newFileName);
            $request['image'] = $newFileName;
        }

        $produit->update($request->toArray());
        return response()->json([
            'data' => $produit,
            'status' => 'success',
            'message' => 'Product updated successfully',
        ]);
    }

    public function destroy($id)
    {
        // Logic to delete a product
        $produit = Produit::find($id);
        if (!$produit) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ], 404);
        }
        // Delete the product
        File::delete('images/produits/' . $produit->image); // Delete the image file if it exists
        $produit->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Product deleted successfully',
        ]);
    }
}
