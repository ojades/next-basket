<?php

namespace App\Http\Controllers;

use App\Events\UserCreated;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->validate([
            'email' => 'string|unique:users,email|email:rfc,dns',
            'first_name' => 'string',
            'last_name' => 'string',
        ]);

        $user = User::create($data);

        UserCreated::dispatch($user);

        return response()->json(data: [
            'successful' => true,
            'data' => $data,
            'message' => 'Queued succefully',
        ]);
    }
}
