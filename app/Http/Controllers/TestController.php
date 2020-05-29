<?php

namespace App\Http\Controllers;


class TestController extends Controller
{
    public function index()
    {
        dd(123);
        dd(\DB::connection()->table('users')->count());


        dd(Parser::all()->count());

    }
}
