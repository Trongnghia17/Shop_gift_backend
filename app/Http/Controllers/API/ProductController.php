<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{

    // xem sản phẩm
    public function index(Request $request)
    {
        $query = Product::query();
        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        $products = $query->get();
        return response()->json([
            'status' => Response::HTTP_OK,
            'products' => $products
        ]);
    }
    // tạo sản phẩm
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'category_id' => 'required|max:191',
                'slug' => 'required|max:191|unique:categories,slug',
                'name' => 'required|max:191',
                'brand' => 'required|max:20',
                'selling_price' => 'required|max:20',
                'original_price' => 'required|max:20',
                'quantity' => 'required|max:4',
                'image' => 'image|mimes:jpeg,png,jpg|max:15360',
            ],
            [
                'required'  => 'Bạn phải điền :attribute',
                'unique'  => 'Slug đã tồn tại!',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        } else {
            $product = new Product;
            $product->category_id = $request->input('category_id');
            $product->slug = $request->input('slug');
            $product->name = $request->input('name');
            $product->description = $request->input('description');

            $product->brand = $request->input('brand');
            $product->selling_price = $request->input('selling_price');
            $product->original_price = $request->input('original_price');
            $product->quantity = $request->input('quantity');
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                $file->move('uploads/product/', $filename);
                $product->image = 'uploads/product/' . $filename;
            }
            $product->featured = $request->input('featured') == true ? '1' : '0';
            $product->popular = $request->input('popular') == true ? '1' : '0';
            $product->status = $request->input('status');
            $product->save();
            return response()->json([
                'status' => 200,
                'message' => 'Thêm sản phẩm thành công.',
            ]);
        }
    }
    // xem sản phẩm để sửa
    public function edit($id)
    {
        $product = Product::find($id);
        if ($product) {
            return response()->json([
                'status' => 200,
                'product' => $product,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy sản phẩm này!',
            ]);
        }
    }
    // sửa sản phẩm
    public function update(Request $request, $id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'category_id' => 'required|max:191',
                'slug' => 'required|max:191|unique:categories,slug',
                'name' => 'required|max:191',
                'brand' => 'required|max:20',
                'selling_price' => 'required|max:20',
                'original_price' => 'required|max:20',
                'quantity' => 'required|max:4',
            ],
            [
                'required'  => 'Bạn phải điền :attribute',
                'unique'  => 'Slug đã tồn tại!',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        } else {
            $product = Product::find($id);
            if ($product) {
                $product->category_id = $request->input('category_id');
                $product->slug = $request->input('slug');
                $product->name = $request->input('name');
                $product->description = $request->input('description');

                $product->brand = $request->input('brand');
                $product->selling_price = $request->input('selling_price');
                $product->original_price = $request->input('original_price');
                $product->quantity = $request->input('quantity');

                if ($request->hasFile('image')) {
                    $path = $product->image;
                    if (File::exists($path)) {
                        File::delete($path);
                    }
                    $file = $request->file('image');
                    $extension = $file->getClientOriginalExtension();
                    $filename = time() . '.' . $extension;
                    $file->move('uploads/product/', $filename);
                    $product->image = 'uploads/product/' . $filename;
                }
                $product->featured = $request->input('featured', '0');
                $product->popular = $request->input('popular', '0');
                $product->status = $request->input('status');
                $product->update();
                return response()->json([
                    'status' => 200,
                    'message' => 'Cập nhật sản phẩm thành công.',
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Không tìm thấy sản phẩm này!',
                ]);
            }
        }
    }
    // xoa product
    public function destroy($id)
    {
        $product = Product::find($id);
        if ($product) {
//            $path = $product->image;
//            if (File::exists($path)) {
//                File::delete($path);
//            }
            $product->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Đã xóa sản phẩm.'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy id của sản phẩm!'
            ]);
        }
    }
}
