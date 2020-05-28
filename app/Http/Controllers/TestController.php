<?php

namespace App\Http\Controllers;

use App\models\Parser;

class TestController extends Controller
{
    public function index()
    {
        dd(Parser::all()->count());

    }
}
