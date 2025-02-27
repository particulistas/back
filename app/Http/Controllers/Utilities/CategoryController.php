<?php

namespace App\Http\Controllers\Utilities;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    
    /**
    * @OA\Get(
    *   path="/v1/list/categories",
    *   summary="Get all categories with their children",
    *   tags={"Categories"},
    *   @OA\Response(
    *     response=200,
    *     description="Categories retrieved successfully",
    *     @OA\JsonContent(
    *       @OA\Property(property="success", type="boolean"),
    *       @OA\Property(property="message", type="array",
    *         @OA\Items(
    *           @OA\Property(property="id", type="integer"),
    *           @OA\Property(property="name", type="string"),
    *           @OA\Property(property="parent_id", type="integer", nullable=true),
    *           @OA\Property(property="children", type="array",
    *             @OA\Items(
    *               @OA\Property(property="id", type="integer"),
    *               @OA\Property(property="name", type="string"),
    *               @OA\Property(property="parent_id", type="integer"),
    *             ),
    *           ),
    *         ),
    *       ),
    *     ),
    *   ),
    *   @OA\Response(
    *     response=400,
    *     description="Invalid input",
    *     @OA\JsonContent(
    *       @OA\Property(property="success", type="boolean"),
     *       @OA\Property(property="message", type="string")
    *     )
    *   )
    * )
    */

    public function show(Category $category)
    {
        $categories = Category::whereNull('parent_id')->with('children')->get();

        return response()->json([
            'success' => true,
            'message' => $categories,
        ], 200);
    }

    public function getMainCategories() {
        $categories = Category::whereNull('parent_id')->get();
        return response()->json($categories);
    }

  /*   public function index()
    {
        $yearVehicles = YearVehicle::where('disabled','=', 0)->orderBy('name', 'asc')->get();
        return response()->json($yearVehicles);
    } */

    public function getChildCategories($parentId) {
        $categories = Category::where('parent_id', $parentId)->get();
        return response()->json($categories);
    }
}