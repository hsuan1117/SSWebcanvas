<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, RefreshDatabase;

    public function upload(Request $request)
    {
        $file = $request->file('file');
        $token = Str::random(6);
        $path = $file->storePublicly('public');
        Photo::create([
            'token' => $token,
            'path' => Str::replace('public/', '', $path)
        ]);
        return response()->json([
            'token' => $token,
        ], 201);
    }

    public function query(Request $request)
    {
        $request->validate([
            'token' => 'required|exists:photos,token'
        ]);

        $photo = Photo::where('token', $request->input('token'))->first();
        return response()->json($photo);
    }
}
