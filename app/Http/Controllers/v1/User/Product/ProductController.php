<?php

namespace App\Http\Controllers\v1\User\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Auth;
use App\Models\Product;
use App\Traits\CustomValidator;
use App\Traits\ResponseHandler;

class ProductController extends Controller
{

    use ResponseHandler;

    public function __construct(Product $product)
    {
        $this->model = $product;
    }

    public function index(Request $request)
    {
        $products = $this->model->searchProducts($request);

        return $this->buildSuccess('success', $products, 'Products loaded successfully', Response::HTTP_OK);
    }
}
