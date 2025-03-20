<?php

namespace App\Http\Controllers;

use App\Http\Middleware\CredentialOwner;
use App\Models\Credential;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CredentialController extends Controller
{
    public function __construct()
    {
        $this->middleware([CredentialOwner::class])->only('show');
    }


    public function show(Credential $credential)
    {
        return response()->json($credential);
    }

    public function store(Request $request)
    {
        $credential = Credential::create($request->all());
        return response()->json($credential, 201);
    }
}
