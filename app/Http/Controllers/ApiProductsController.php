<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ApiProductsController extends Controller
{
    public function list () {
        return Product::get();
    }
}
