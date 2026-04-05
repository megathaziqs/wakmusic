<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    /**
     * Get all playlists for the user
     */
    public function index()
    {
        // TODO: Return user's playlists from database
        return response()->json([
            'data' => [
                ['id' => 1, 'name' => 'My Favorites', 'description' => 'My all-time favorite songs', 'songs_count' => 10],
                ['id' => 2, 'name' => 'Workout Mix', 'description' => 'High energy tracks', 'songs_count' => 5],
            ]
        ]);
    }

    /**
     * Create a new playlist
     */
    public function store(Request $request)
    {
        // TODO: Validate and create playlist
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        return response()->json(['message' => 'Playlist created successfully'], 201);
    }

    /**
     * Get songs in a specific playlist
     */
    public function getSongs($id)
    {
        // TODO: Get playlist with songs
        return response()->json([
            'id' => $id,
            'name' => 'Playlist Name',
            'songs' => [
                ['id' => 1, 'title' => 'Song 1', 'artist' => 'Artist 1'],
                ['id' => 2, 'title' => 'Song 2', 'artist' => 'Artist 2'],
            ]
        ]);
    }

    /**
     * Add song to playlist
     */
    public function addSong(Request $request, $playlistId, $songId)
    {
        // TODO: Add song to playlist
        return response()->json(['message' => 'Song added to playlist']);
    }

    /**
     * Delete playlist
     */
    public function destroy($id)
    {
        // TODO: Delete playlist from database
        return response()->json(['message' => 'Playlist deleted successfully']);
    }
}
