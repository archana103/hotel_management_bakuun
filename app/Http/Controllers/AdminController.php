<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeAdminMail;
class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            return User::role('admin')->paginate(10);  // Make sure 'admin' role exists
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching admins',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => ['required', Rule::in(['users', 'admin','master'])]
        ]);
    
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
     // Send email only to admin or master
     if (in_array($request->role, ['admin'])) {
        Mail::to($user->email)->send(new WelcomeAdminMail($user, $request->password));
     }

        
        $user->syncRoles([$request->role]); 
    
        return response()->json([
            'user' => $user,
             'roles' => $user->getRoleNames()
        ], 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $admin = User::findOrFail($id);
    
        // Optional: Validate input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,$id",
        ]);
    
        $admin->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);
    
        return response()->json(['message' => 'Admin updated successfully']);
    }
    

public function destroy($id)
{
    $admin = User::findOrFail($id);
    $admin->delete();
    return response()->json(['message' => 'Admin deleted successfully']);
}
}
