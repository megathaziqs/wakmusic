<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SongController extends Controller
{
    /**
     * Display a listing of songs
     */
    public function index()
    {
        // TODO: Return paginated list of songs from database
        return response()->json([
            'data' => [
                ['id' => 1, 'title' => 'Bohemian Rhapsody', 'artist' => 'Queen', 'album' => 'A Night at the Opera'],
                ['id' => 2, 'title' => 'Imagine', 'artist' => 'John Lennon', 'album' => 'Imagine'],
            ],
            'total' => 2
        ]);
    }

    /**
     * Store a newly created song
     */
    public function store(Request $request)
    {
        // TODO: Validate input and save song to database
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'artist' => 'required|string|max:255',
            'album' => 'nullable|string|max:255',
        ]);

        // TODO: Create song in database
        return response()->json(['message' => 'Song created successfully'], 201);
    }

    /**
     * Display the specified song
     */
    public function show($id)
    {
        // TODO: Get song from database
        return response()->json([
            'id' => $id,
            'title' => 'Song Title',
            'artist' => 'Artist Name',
            'album' => 'Album Name'
        ]);
    }

    /**
     * Update the specified song
     */
    public function update(Request $request, $id)
    {
        // TODO: Validate and update song
        return response()->json(['message' => 'Song updated successfully']);
    }

    /**
     * Delete the specified song
     */
    public function destroy($id)
    {
        // TODO: Delete song from database
        return response()->json(['message' => 'Song deleted successfully']);
    }
}
