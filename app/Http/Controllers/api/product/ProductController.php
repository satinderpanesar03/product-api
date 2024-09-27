<?php

namespace App\Http\Controllers\api\product;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Trait\ApiResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with(['variation_name','variations:id,product_id,name', 'variationCombinations.variations']);

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->input('price_min'));
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->input('price_max'));
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        if ($request->filled('sort_by_price')) {
            $query->orderBy('price', $request->sort_by_price);
        }

        if ($request->filled('sort_by_stock_quantity')) {
            $query->orderBy('stock_quantity', $request->sort_by_stock_quantity);
        }
//return $query->first();
        $products = ProductResource::collection($query->get());

        return $this->successResponse('Products retrieved successfully.', $products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock_quantity' => 'required|integer',
            'media' => 'nullable|array',
            'media.*' => 'file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,avi|max:20480',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422,);
        }

        $mediaPaths = [];
        if ($request->has('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('products/media');
                $mediaPaths[] = $path;
            }
        }

        $validated = $validator->validated();
        $validated['media'] = json_encode($mediaPaths);

        try {
            $product = Product::create($validated);
            return $this->successResponse('Product created successfully.', $product, 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create product: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with(['variations:id,product_id,name', 'variationCombinations.variations'])->find($id);

        if (!$product) {
            return $this->errorResponse('Product not found.', 404);
        }

        return $this->successResponse('Product retrieved successfully.', new ProductResource($product));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return $this->errorResponse('Product not found.', 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric',
                'stock_quantity' => 'required|integer',
                'media' => 'nullable|array',
                'media.*' => 'file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,avi|max:20480',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $validated = $validator->validated();
            $mediaPaths = json_decode($product->media, true);

            if ($request->has('media')) {
                foreach ($request->file('media') as $file) {
                    $path = $file->store('products/media');
                    $mediaPaths[] = $path;
                }
            }

            $validated['media'] = json_encode($mediaPaths);
            $product->update($validated);

            return $this->successResponse('Product updated successfully.', $product);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update product: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return $this->errorResponse('Product not found.', 404);
            }

            $mediaPaths = json_decode($product->media, true);
            if ($mediaPaths) {
                foreach ($mediaPaths as $path) {
                    Storage::delete($path);
                }
            }

            $product->delete();

            return $this->successResponse('Product deleted successfully.', [], 200);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete product: ' . $e->getMessage(), 500);
        }
    }
}
