<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Institution;
use App\Models\Supplier;

class AdminController extends Controller
{


    private function authorizeAdmin($user)
    {
        if ($user->id_rol !== 2) {
            abort(403, 'Acceso no autorizado');
        }
    }

    // --------- CATEGORY ---------
    public function storeCategory(Request $request)
    {
        $this->authorizeAdmin($request->user());

        $request->validate(['name' => 'required|string']);
        $category = Category::create(['name' => $request->name]);

        return response()->json($category, 201);
    }

    public function updateCategory(Request $request, Category $category)
    {
        $this->authorizeAdmin($request->user());

        $request->validate(['name' => 'required|string']);
        $category->update(['name' => $request->name]);

        return response()->json($category);
    }

    public function destroyCategory(Request $request, Category $category)
    {
        $this->authorizeAdmin($request->user());

        $category->delete();
        return response()->json(['message' => 'Categoría eliminada']);
    }

    public function indexCategory()
    {
        return response()->json(Category::all());
    }

    // --------- INSTITUTION ---------
    public function storeInstitution(Request $request)
    {
        $this->authorizeAdmin($request->user());

        $request->validate(['name' => 'required|string']);
        $institution = Institution::create(['name' => $request->name]);

        return response()->json($institution, 201);
    }

    public function updateInstitution(Request $request, Institution $institution)
    {
        $this->authorizeAdmin($request->user());

        $request->validate(['name' => 'required|string']);
        $institution->update(['name' => $request->name]);

        return response()->json($institution);
    }

    public function destroyInstitution(Request $request, Institution $institution)
    {
        $this->authorizeAdmin($request->user());

        $institution->delete();
        return response()->json(['message' => 'Institución eliminada']);
    }

    public function indexInstitution()
    {
        return response()->json(Institution::all());
    }

    // --------- SUPPLIER ---------
    public function storeSupplier(Request $request)
    {
        $this->authorizeAdmin($request->user());

        $request->validate(['name' => 'required|string']);
        $supplier = Supplier::create(['name' => $request->name]);

        return response()->json($supplier, 201);
    }

    public function updateSupplier(Request $request, Supplier $supplier)
    {
        $this->authorizeAdmin($request->user());

        $request->validate(['name' => 'required|string']);
        $supplier->update(['name' => $request->name]);

        return response()->json($supplier);
    }

    public function destroySupplier(Request $request, Supplier $supplier)
    {
        $this->authorizeAdmin($request->user());

        $supplier->delete();
        return response()->json(['message' => 'Proveedor eliminado']);
    }

    public function indexSupplier()
    {
        return response()->json(Supplier::all());
    }
}
