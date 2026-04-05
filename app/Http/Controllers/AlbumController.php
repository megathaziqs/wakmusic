<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    public function index()
    {
        return response()->json(Album::with('songs')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'artist' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|string',
        ]);

        $album = Album::create($validated);

        return response()->json($album);
    }

    public function show(Album $album)
    {
        return response()->json($album->load('songs'));
    }

    public function update(Request $request, Album $album)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'artist' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|string',
        ]);

        $album->update($validated);

        return response()->json($album);
    }

    public function destroy(Album $album)
    {
        $album->delete();

        return response()->json(['success' => true]);
    }
}
