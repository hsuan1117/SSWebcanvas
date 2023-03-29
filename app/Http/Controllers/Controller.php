<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Hash;
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
            'path' => Str::replace('public/', '', $path),
            'password' =>
                $request->has('password') ? Hash::make($request->input('password')) : null,
        ]);
        return response()->json([
            'token' => $token,
        ], 201);
    }

    public function query(Request $request)
    {
        $request->validate([
            'token' => 'required|exists:photos,token',
            'password' => 'nullable'
        ]);

        $photo = Photo::where('token', $request->input('token'))->first();

        if ($photo->password !== null) {
            if (!$request->has('password')) {
                return response()->json([
                    'message' => 'Password required',
                ], 401);
            }
            if (!Hash::check($request->input('password'), $photo->password)) {
                return response()->json([
                    'message' => 'Password incorrect',
                ], 401);
            }
        }

        return response()->json($photo);
    }
}
