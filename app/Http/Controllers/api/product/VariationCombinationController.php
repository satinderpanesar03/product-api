<?php

namespace App\Http\Controllers\api\product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\VariationCombination;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VariationCombinationController extends Controller
{
    use ApiResponse;

    public function index($productId)
    {
        $combinations = VariationCombination::where('product_id', $productId)->with('variations')->get();
        return $this->successResponse('Variation combinations retrieved successfully.', $combinations);
    }

    public function store(Request $request, $productId)
    {
        $validator = Validator::make($request->all(), [
            'variations' => 'required|array',
            'variations.*.variation_id' => 'required|exists:variations,id',
            'variations.*.value' => 'required|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
        ]);


        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        $product = Product::find($productId);
        if (!$product) {
            return $this->errorResponse('Product not found.', 404);
        }

        DB::beginTransaction();

        try {
            $combination = VariationCombination::create([
                'product_id' => $productId,
                'price' => $request->price,
                'stock_quantity' => $request->quantity,
            ]);

            $this->attachVariations($combination, $request->variations, $productId);

            DB::commit();

            return $this->successResponse('Variation combination created successfully.', $combination, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function show($productId, $id)
    {
        $combination = VariationCombination::where('product_id', $productId)->with('variations')->find($id);

        if (!$combination) {
            return $this->errorResponse('Variation combination not found for this product.', 404);
        }

        return $this->successResponse('Variation combination retrieved successfully.', $combination);
    }

    public function update(Request $request, $productId, $combinationId)
    {
        $validator = Validator::make($request->all(), [
            'variations' => 'required|array',
            'variations.*.variation_id' => 'required|exists:variations,id',
            'variations.*.value' => 'required|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        $product = Product::find($productId);
        if (!$product) {
            return $this->errorResponse('Product not found.', 404);
        }

        $combination = VariationCombination::find($combinationId);
        if (!$combination) {
            return $this->errorResponse('Variation combination not found.', 404);
        }

        DB::beginTransaction();

        try {
            $combination->update([
                'price' => $request->price,
                'stock_quantity' => $request->quantity,
            ]);

            DB::commit();

            return $this->successResponse('Variation combination updated successfully.', $combination);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function destroy($productId, $id)
    {
        if (!Product::find($productId)) {
            return $this->errorResponse('Product not found.', 404);
        }

        $combination = VariationCombination::where('product_id', $productId)->find($id);
        if (!$combination) {
            return $this->errorResponse('Variation combination not found for this product.', 404);
        }

        try {
            $combination->delete();
            return $this->successResponse('Variation combination deleted successfully.', [], 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    private function attachVariations(VariationCombination $combination, array $variations, $productId)
    {
        foreach ($variations as $variation) {
            if (is_array($variation) && isset($variation['variation_id'], $variation['value'])) {
                $combination->variations()->attach($variation['variation_id'], [
                    'value' => $variation['value'],
                    'product_id' => $productId,
                ]);
            } else {
                throw new \InvalidArgumentException('Invalid variation format.');
            }
        }
    }
}
