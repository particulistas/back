<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


use App\Models\Tenant;
use App\Models\TenantPeople;

class TenantController extends Controller
{
    /**
     * Obtener tenant por user_id
     */
    public function getByUserId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id'
            
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $tenant = Tenant::with('peoples')
            ->where('user_id', $request->user_id)
            ->where('room', $request->room)
            ->first();

        return response()->json($tenant ? [$tenant] : []);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'room' => 'nullable|boolean',
            'pets' => 'nullable|json',
            'accept_no_smoking' => 'nullable|boolean',
            'can_provide_documentation' => 'nullable|boolean',
            'can_provide_references' => 'nullable|boolean',
            'no_credit_issues' => 'nullable|boolean',
            'not_real_estate_professional' => 'nullable|boolean',
            'additional_info' => 'nullable|string',
            'income_percentage' => 'nullable|string|max:20',
            'minimum_stay' => 'nullable|string|max:20',
            'peoples' => 'nullable|array',
            'peoples.*.name' => 'required_with:peoples|string|max:50',
            'peoples.*.age' => 'required_with:peoples|integer|min:0',
            'peoples.*.employment_situation' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->all();
        $data['pets'] = $request->pets ?? json_encode([]);

        $tenant = Tenant::create($data);

        if ($request->has('peoples')) {
            foreach ($request->peoples as $person) {
                $tenant->peoples()->create([
                    'name' => $person['name'],
                    'age' => $person['age'],
                    'employment_situation' => $person['employment_situation'] ?? null
                ]);
            }
        }

        return response()->json($tenant->load('peoples'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tenant = Tenant::with('peoples')->findOrFail($id);
        return response()->json($tenant);
    }

    /**
     * Update the specified resource in storage.
     */
   /* public function update(Request $request, string $id)
    {
        $tenant = Tenant::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'user_id' => 'sometimes|exists:users,id',
            'room' => 'nullable|boolean',
            'pets' => 'nullable|json',
            'accept_no_smoking' => 'nullable|boolean',
            'can_provide_documentation' => 'nullable|boolean',
            'can_provide_references' => 'nullable|boolean',
            'no_credit_issues' => 'nullable|boolean',
            'not_real_estate_professional' => 'nullable|boolean',
            'additional_info' => 'nullable|string',
            'income_percentage' => 'nullable|string|max:20',
            'minimum_stay' => 'nullable|string|max:20',
            'peoples' => 'nullable|array',
           // 'peoples.*.id' => 'sometimes|exists:tenant_peoples,id',
            // ... otras validaciones
             'peoples.*.id' => 'sometimes|nullable|exists:tenant_peoples,id', // Permitir null para nuevos registros

            'peoples.*.name' => 'sometimes|string|max:50',
            'peoples.*.age' => 'sometimes|integer|min:0',
            'peoples.*.employment_situation' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->all();
        if ($request->has('pets')) {
            $data['pets'] = $request->pets;
        }

        $tenant->update($data);

        // Sincronizar peoples
        if ($request->has('peoples')) {
            $existingPeopleIds = $tenant->peoples->pluck('id')->toArray();
            $updatedPeopleIds = [];
            
            foreach ($request->peoples as $person) {
                if (isset($person['id']) && in_array($person['id'], $existingPeopleIds)) {
                    // Actualizar existente
                    $tenant->peoples()
                        ->where('id', $person['id'])
                        ->update([
                            'name' => $person['name'],
                            'age' => $person['age'],
                            'employment_situation' => $person['employment_situation'] ?? null
                        ]);
                    $updatedPeopleIds[] = $person['id'];
                } else {
                    // Crear nuevo
                    $newPerson = $tenant->peoples()->create([
                        'name' => $person['name'],
                        'age' => $person['age'],
                        'employment_situation' => $person['employment_situation'] ?? null
                    ]);
                    $updatedPeopleIds[] = $newPerson->id;
                }
            }
            
            // Eliminar los que no están en el request
            $toDelete = array_diff($existingPeopleIds, $updatedPeopleIds);
            if (!empty($toDelete)) {
                $tenant->peoples()->whereIn('id', $toDelete)->delete();
            }
        }

        return response()->json($tenant->fresh()->load('peoples'));
    }*/

    public function update(Request $request, string $id)
{
    $tenant = Tenant::findOrFail($id);

    $validator = Validator::make($request->all(), [
        'user_id' => 'sometimes|exists:users,id',
        'room' => 'nullable|boolean',
        'pets' => 'nullable|json',
        'accept_no_smoking' => 'nullable|boolean',
        'can_provide_documentation' => 'nullable|boolean',
        'can_provide_references' => 'nullable|boolean',
        'no_credit_issues' => 'nullable|boolean',
        'not_real_estate_professional' => 'nullable|boolean',
        'additional_info' => 'nullable|string',
        'income_percentage' => 'nullable|string|max:20',
        'minimum_stay' => 'nullable|string|max:20',
        'peoples' => 'nullable|array',
        'peoples.*.name' => 'required_with:peoples|string|max:50',
        'peoples.*.age' => 'required_with:peoples|integer|min:0',
        'peoples.*.employment_situation' => 'nullable|string|max:20',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    $data = $request->all();
    if ($request->has('pets')) {
        $data['pets'] = $request->pets;
    }

    // Actualizar datos principales del tenant
    $tenant->update($data);

    // Eliminar TODOS los peoples existentes para este tenant
    $tenant->peoples()->delete();

    // Crear NUEVOS peoples con los datos del request
    if ($request->has('peoples') && is_array($request->peoples)) {
        foreach ($request->peoples as $person) {
            $tenant->peoples()->create([
                'name' => $person['name'],
                'age' => $person['age'],
                'employment_situation' => $person['employment_situation'] ?? null
            ]);
        }
    }

    return response()->json($tenant->fresh()->load('peoples'));
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->delete();
        return response()->json(null, 204);
    }
}
