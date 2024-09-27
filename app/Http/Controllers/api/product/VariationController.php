<?php

namespace App\Http\Controllers\api\product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Variation;
use Illuminate\Http\Request;
use App\Trait\ApiResponse;
use Illuminate\Support\Facades\Validator;

class VariationController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index($productId)
    {
        $variations = Variation::where('product_id', $productId)->get();
        return $this->successResponse('Variations retrieved successfully.', $variations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $productId)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        if (!Product::find($productId)) {
            return $this->errorResponse('Product not found.', 404);
        }

        try {
            $variation = Variation::create(array_merge($request->all(), ['product_id' => $productId]));
            return $this->successResponse('Variation created successfully.', $variation, 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create variation: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($productId, $id)
    {
        $variation = Variation::where('product_id', $productId)->find($id);

        if (!$variation) {
            return $this->errorResponse('Variation not found for this product.', 404);
        }

        return $this->successResponse('Variation retrieved successfully.', $variation);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $productId, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        if (!Product::find($productId)) {
            return $this->errorResponse('Product not found.', 404);
        }

        $variation = Variation::where('product_id', $productId)->find($id);
        if (!$variation) {
            return $this->errorResponse('Variation not found for this product.', 404);
        }

        try {
            $variation->update($request->all());
            return $this->successResponse('Variation updated successfully.', $variation);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update variation: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($productId,$id)
    {
        try {
            $variation = Variation::find($id);

            if (!$variation) {
                return $this->errorResponse('Variation not found.', 404);
            }

            $variation->delete();
            return $this->successResponse('Variation deleted successfully.', [], 200);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete variation: ' . $e->getMessage(), 500);
        }
    }
}
