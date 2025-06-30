<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Profile;
use App\Models\User;
use App\Http\Controllers\AvatarController;

class AvatarController extends Controller
{
    public function upload(Request $request)
    {
        // Validar la solicitud
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Máximo 2MB
        ]);

        // Guardar la imagen en la carpeta public/uploads/avatars
        $path = $request->file('avatar')->store('public/uploads/avatars');
        $fileName = basename($path);

        // Devolver el nombre del archivo
        return response()->json(['fileName' => $fileName]);
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'userId' => 'required|exists:users,id',
            'avatar' => 'required|string',
        ]);

        // Actualizar el campo avatar en la tabla profile
        $user = User::find(53);
        $user->profile->updateOrCreate(
            ['user_id' => $user->id],
            ['avatar' => $request->avatar]
        );

        return response()->json(['message' => 'Avatar actualizado correctamente.']);
    }
}