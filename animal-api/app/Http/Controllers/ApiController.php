<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models\User;
use App\Models\Adoption;
use App\Models\Animal;

class ApiController extends Controller
{
    public function handle(Request $request)
    {
        $endpoint = $request->query('endpoint');
        $action   = $request->query('action');

        switch ($endpoint) {
            case 'users':
                return $this->handleUsers($action, $request);
            case 'animals':
                return $this->handleAnimals($action, $request);
            case 'adoptions':
                return $this->handleAdoptions($action, $request);
            default:
                return response()->json(['error' => 'Unknown endpoint'], 404);
        }
    }

    private function requireAuth(Request $request)
    {
        $header = $request->header('Authorization'); // e.g. "Bearer abc123"
        if (!$header || !str_starts_with($header, 'Bearer ')) {
            return null;
        }

        $token = substr($header, 7); // strip "Bearer "
        $accessToken = PersonalAccessToken::findToken($token);

        return $accessToken ? $accessToken->tokenable : null; // returns the User model or null
    }

    private function handleUsers($action, Request $request)
    {

        // Register new user
        if ($action === 'register' && $request->isMethod('post')) {
            try {
                // Validate input
                $validated = $request->validate([
                    'username' => 'required|string|max:50|unique:users',
                    'email'    => 'required|email|max:100|unique:users',
                    'password' => 'required|string|min:8',
                ]);

                // Attempt to create user
                $user = \App\Models\User::create([
                    'username'      => $validated['username'],
                    'email'         => $validated['email'],
                    'password_hash' => \Illuminate\Support\Facades\Hash::make($validated['password']),
                ]);

                if ($user) {
                    return response()->json([
                        'success' => true,
                        'id'      => $user->id,
                    ], 201);
                }

                // If creation failed
                return response()->json([
                    'error' => 'Account creation failed',
                ], 500);

            } catch (\Illuminate\Validation\ValidationException $e) {
                // Validation failed
                return response()->json([
                    'error' => 'Missing required fields',
                    'details' => $e->errors(),
                ], 422);
            } catch (\Exception $e) {
                // Any other error
                return response()->json([
                    'error' => 'Account creation failed',
                    'details' => $e->getMessage(),
                ], 500);
            }
        }

        // Login user → issue Sanctum token
         if ($action === 'login' && $request->isMethod('post')) {
            if (!$request->has('email') || !$request->has('password')) {
                return response()->json(['error' => 'Missing email or password'], 400);
            }

            $user = User::where('email', $request->input('email'))->first();

            if (!$user || !Hash::check($request->input('password'), $user->password_hash)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'token'   => $token, // Bearer token string
            ]);
        }

        return response()->json(['error' => 'Unknown users action'], 400);
    }


     
    private function handleAnimals($action, Request $request)
    {
        // Default: list all animals if no action/id
        if (!$action && !$request->query('id')) {
            return response()->json(\App\Models\Animal::all());
        }

        // Show one animal by ID
        if (!$action && $request->query('id')) {
            $animal = \App\Models\Animal::find($request->query('id'));
            return $animal
                ? response()->json($animal)
                : response()->json(['error' => 'Animal not found'], 404);
        }

        // Create a new animal (POST)
        if ($action === 'create' && $request->isMethod('post')) {
            // Require JWT in Authorization header
            $user = $this->requireAuth($request);
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            try {
                $validated = $request->validate([
                    'name'    => 'required|string|max:100',
                    'species' => 'required|string|max:50',
                    'breed'   => 'nullable|string|max:100',
                    'age'     => 'required|integer|min:0',
                    'gender'  => 'required|in:Male,Female',
                    'status'  => 'required|in:Available,Adopted,Fostered,Transferred',
                ]);

                $animal = \App\Models\Animal::create($validated);

                return response()->json([
                    'success' => true,
                    'id'      => $animal->id,
                ], 201);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Insert failed'], 500);
            }
        }

        // Update an animal (PATCH)
        if ($action === 'update' && $request->isMethod('patch')) {
            // Require JWT in Authorization header
            $user = $this->requireAuth($request);
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $animal = \App\Models\Animal::find($request->query('id'));
            if (!$animal) {
                return response()->json(['error' => 'Animal not found'], 404);
            }

            // Only allow updating certain fields
            $fields = ['name','species','breed','age','gender','status'];
            $updates = $request->only($fields);

            if (empty($updates)) {
                return response()->json(['error' => 'No valid fields provided for update'], 400);
            }

            try {
                $animal->update($updates);

                return response()->json([
                    'success'    => true,
                    'updated_id' => $animal->id
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'error'   => 'Update failed',
                    'details' => $e->getMessage()
                ], 500);
            }
        }

        // Delete an animal (DELETE)
        if ($action === 'delete' && $request->isMethod('delete')) {
            // Require JWT in Authorization header
            $user = $this->requireAuth($request);
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $animal = \App\Models\Animal::find($request->query('id'));
            if (!$animal) {
                return response()->json(['error' => 'Animal not found'], 404);
            }

            try {
                $animal->delete();

                return response()->json([
                    'success'    => true,
                    'deleted_id' => (int) $request->query('id')
                ]);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Delete failed'], 500);
            }
        }

        // Search animals (GET with filters)
        if ($action === 'search') {
            $query = \App\Models\Animal::query();
            foreach (['name','breed','species','age','gender','status'] as $field) {
                if ($request->has($field)) {
                    $query->where($field, $request->query($field));
                }
            }
            return response()->json($query->get());
        }

        return response()->json(['error' => 'Unknown animals action'], 400);
    }



    private function handleAdoptions($action, Request $request)
    {
        // Case 1: GET /index.php?endpoint=adoptions&id=5
        if (!$action && $request->isMethod('get') && $request->query('id')) {
            $adoption = \App\Models\Adoption::find($request->query('id'));

            if (!$adoption) {
                return response()->json(['error' => 'Adoption request not found'], 404);
            }

            return response()->json($adoption);
        }

        // Case 2: POST /index.php?endpoint=adoptions
        if (!$action && $request->isMethod('post')) {
            try {
                // Validate required fields
                $validated = $request->validate([
                    'animal_id'       => 'required|integer',
                    'adopter_name'    => 'required|string|max:100',
                    'adopter_phone'   => 'required|string|max:20',
                    'adopter_email'   => 'required|email|max:100',
                    'adopter_address' => 'required|string|max:255',
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json(['error' => 'Missing required fields'], 400);
            }

            // Check if animal exists
            $animal = \App\Models\Animal::find($validated['animal_id']);
            if (!$animal) {
                return response()->json(['error' => 'Invalid animal_id: no such animal exists'], 400);
            }

            try {
                $adoption = \App\Models\Adoption::create([
                    'animal_id'       => $validated['animal_id'],
                    'adoption_date'   => now()->toDateString(),
                    'adopter_name'    => $validated['adopter_name'],
                    'adopter_phone'   => $validated['adopter_phone'],
                    'adopter_email'   => $validated['adopter_email'],
                    'adopter_address' => $validated['adopter_address'],
                    'status'          => 'pending',
                ]);

                return response()->json([
                    'success' => true,
                    'id'      => $adoption->id
                ], 201);
            } catch (\Exception $e) {
                return response()->json([
                    'error'   => 'Adoption request failed',
                    'details' => $e->getMessage()
                ], 500);
            }
        }

        // Case 3: PATCH /index.php?endpoint=adoptions&action=update&id=5
        if ($action === 'update' && $request->isMethod('patch')) {
            // Require JWT in Authorization header
            $user = $this->requireAuth($request);
            if (!$user) {
                return response()->json(['error' => 'Invalid or expired token'], 401);
            }

            $adoption = \App\Models\Adoption::find($request->query('id'));
            if (!$adoption) {
                return response()->json(['error' => 'Adoption request not found'], 404);
            }

            // Only allow updating certain fields
            $fields = ['status','adopter_name','adopter_phone','adopter_email','adopter_address'];
            $updates = $request->only($fields);

            if (empty($updates)) {
                return response()->json(['error' => 'No valid fields provided for update'], 400);
            }

            try {
                $adoption->update($updates);

                return response()->json([
                    'success'    => true,
                    'updated_id' => $adoption->id
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'error'   => 'Failed to update adoption request',
                    'details' => $e->getMessage()
                ], 500);
            }
        }

        // Default fallback
        return response()->json(['error' => 'Unknown adoptions action'], 400);
    }

}
