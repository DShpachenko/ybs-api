<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Modules\Auth\Auth;

class Controller extends BaseController
{
    use Auth;
}
