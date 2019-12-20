<?php

namespace App\Http\Controllers\apis;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TestApiController extends Controller
{
    // Just for test //
    public function test(){
      return "hello, im working.";
    }
}
