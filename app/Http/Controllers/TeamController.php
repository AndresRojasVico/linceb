<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;

class TeamController extends Controller
{
    //
    public function index()
    {

        $users = User::where('company_id', auth()->user()->company_id)
            ->withCount('projects')
            ->with('role')
            ->get();

        return view('team.index', compact('users'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:50',
            'surname'  => 'nullable|string|max:50',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $userRole = Role::where('name', 'User')->first();

        User::create([
            ...$validated,
            'password'   => bcrypt($validated['password']),
            'role_id'    => $userRole->id,
            'company_id' => auth()->user()->company_id,
        ]);

        return redirect()->route('index')->with('success', 'Miembro añadido correctamente.');
    }
}
