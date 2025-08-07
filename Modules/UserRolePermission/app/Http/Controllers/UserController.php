<?php

namespace Modules\UserRolePermission\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\UserRolePermission\app\Http\Requests\UserRequest;
use App\Traits\Upload;
use Modules\LocationManagement\app\Models\City;
use Modules\LocationManagement\app\Models\Country;
use Modules\LocationManagement\app\Models\State;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use Upload;

    protected $roleName = null;
    public function index()
    {
        if (request()->routeIs('admin.drivers.index')) {
            $this->roleName = 'driver';
        } elseif (request()->routeIs('admin.parents.index')) {
            $this->roleName = 'parent';
        }

        $countries = Country::where('status', 'active')->get();
        $roles = Role::get();

        return view('userrolepermission::index', [
                    'countries' => $countries,
                    'roles' => $roles,
                    'roleName' => $this->roleName,
                ]);

    }

    public function store(UserRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated(); // ✅ safer than manually pulling fields

            if($request->file('photo'))
            {
                $data['photo'] = $this->uploadFile($request->file('photo'), 'user');
            }

            // Create user
            $user = User::create([
                'first_name'        => $data['first_name'],
                'last_name'         => $data['last_name'],
                'email'             => $data['email'],
                'password'          => bcrypt($data['password']),
                'phone'             => $data['phone'] ?? null,
                'dob'               => $data['dob'] ?? null,
                'gender'            => $data['gender'] ?? null,
                'height_cm'         => $data['height_cm'] ?? null,
                'weight_kg'         => $data['weight_kg'] ?? null,
                'address'           => $data['address'] ?? null,
                'photo'             => $data['photo'],
                'country_id'        => $data['country_id'] ?? null,
                'state_id'          => $data['state_id'] ?? null,
                'city_id'           => $data['city_id'] ?? null,
                'status'            => $data['status'],
            ]);

            $user->assignRole($data['role']);

            // If driver, store extra fields
            if ($data['role'] === 'driver') {

                // Store uploaded images
                if($request->file('driving_license_image'))
                {
                    $data['driving_license_image'] = $this->uploadFile($request->file('driving_license_image'), 'driver/'. $user->id);
                }
                if($request->file('car_image'))
                {
                    $data['car_image'] = $this->uploadFile($request->file('car_image'), 'driver/'. $user->id);
                }

                $user->driver()->create([
                    'driving_license_number' => $data['driving_license_number'],
                    'driving_license_expiry' => $data['driving_license_expiry'],
                    'driving_license_image'  => $data['driving_license_image'] ?? '',
                    'car_model'              => $data['car_model'],
                    'car_make'               => $data['car_make'],
                    'car_year'               => $data['car_year'],
                    'car_color'              => $data['car_color'],
                    'car_plate_number'       => $data['car_plate_number'],
                    'car_image'              => $data['car_image'] ?? '',
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'User created and role assigned successfully',
                'user' => $user->load('driver') // if applicable
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('User creation failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit(User $user)
    {
        $role = $user->getRoleNames()->first();

        $data = [
            'id'         => $user->id,
            'first_name' => $user->first_name,
            'last_name'  => $user->last_name,
            'email'      => $user->email,
            'phone'      => $user->phone,
            'dob'        => $user->dob,
            'gender'     => $user->gender,
            'height_cm'  => $user->height_cm,
            'weight_kg'  => $user->weight_kg,
            'photo'      => $user->photo ? asset($user->photo) : null,
            'address'    => $user->address,
            'status'     => $user->status,
            'role'       => $role,
            'country_id' => $user->country_id,
            'state_id'   => $user->state_id,
            'city_id'    => $user->city_id,
        ];

        // If role is driver, add driver-specific fields
        if ($role === 'driver') {
            $driver = $user->driver;
            $data = array_merge($data, [
                'driving_license_number'  => $driver->driving_license_number,
                'driving_license_expiry'  => $driver->driving_license_expiry,
                'driving_license_image'   => $driver->driving_license_image ? asset($driver->driving_license_image) : null,
                'car_model'               => $driver->car_model,
                'car_make'                => $driver->car_make,
                'car_year'                => $driver->car_year,
                'car_color'               => $driver->car_color,
                'car_plate_number'        => $driver->car_plate_number,
                'car_image'               => $driver->car_image ? asset($driver->car_image) : null,
            ]);
        }

        return response()->json($data);
    }

    public function show(User $user)
    {
        $role = $user->getRoleNames()->first();

        $data = [
            'id'         => $user->id,
            'first_name' => $user->first_name,
            'last_name'  => $user->last_name,
            'email'      => $user->email,
            'phone'      => $user->phone,
            'dob'        => $user->dob,
            'gender'     => $user->gender,
            'height_cm'  => $user->height_cm,
            'weight_kg'  => $user->weight_kg,
            'photo'      => $user->photo ? asset($user->photo) : null,
            'address'    => $user->address,
            'status'     => $user->status,
            'role'       => $role,
            'country_id' => $user->country_name,
            'state_id'   => $user->state_name,
            'city_id'    => $user->city_name,
        ];

        // If role is driver, add driver-specific fields
        if ($role === 'driver') {
            $driver = $user->driver;
            $data = array_merge($data, [
                'driving_license_number'  => $driver->driving_license_number,
                'driving_license_expiry'  => $driver->driving_license_expiry,
                'driving_license_image'   => $driver->driving_license_image ? asset($driver->driving_license_image) : null,
                'car_model'               => $driver->car_model,
                'car_make'                => $driver->car_make,
                'car_year'                => $driver->car_year,
                'car_color'               => $driver->car_color,
                'car_plate_number'        => $driver->car_plate_number,
                'car_image'               => $driver->car_image ? asset($driver->car_image) : null,
            ]);
        }

        if ($role === 'parent') {
            $kids = $user->kids;  // Collection of all kids

            $data['kids'] = $kids->map(function ($kid) {
                return [
                    'kid_id'           => $kid->id,
                    'first_name'       => $kid->first_name,
                    'last_name'        => $kid->last_name,
                    'dob'              => $kid->dob,
                    'gender'           => $kid->gender,
                    'height_cm'        => $kid->height_cm,
                    'weight_kg'        => $kid->weight_kg,
                    'photo'            => $kid->photo ? asset($kid->photo) : null,
                    'school_name'      => $kid->school_name,
                    'school_address'   => $kid->school_address,
                    'emergency_contact'=> $kid->emergency_contact,
                ];
            })->toArray();
        }


        return response()->json($data);
    }

    public function update(UserRequest $request, User $user)
    {
        // return $request;

        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Upload new profile photo if provided
            if ($request->file('photo')) {
                $data['photo'] = $this->uploadFile($request->file('photo'), 'user');
                if($user->photo) $this->deleteFile($user->photo);
            }

            // Update user fields
            $user->update([
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
                'email'      => $data['email'],
                'phone'      => $data['phone'] ?? null,
                'dob'        => $data['dob'] ?? null,
                'gender'     => $data['gender'] ?? null,
                'height_cm'  => $data['height_cm'] ?? null,
                'weight_kg'  => $data['weight_kg'] ?? null,
                'address'    => $data['address'] ?? null,
                'photo'      => $data['photo'] ?? $user->photo,
                'country_id' => $data['country_id'] ?? null,
                'state_id'   => $data['state_id'] ?? null,
                'city_id'    => $data['city_id'] ?? null,
                'status'     => $data['status'],
            ]);

            // Update password if provided
            if ($request->filled('password')) {
                $user->password = bcrypt($data['password']);
                $user->save();
            }

            // Sync role
            $user->syncRoles($data['role']);

            // If driver, update or create driver info
            if ($data['role'] === 'driver') {
                if ($request->file('driving_license_image')) {
                    $data['driving_license_image'] = $this->uploadFile($request->file('driving_license_image'), 'driver/' . $user->id);
                    if($user->driver?->driving_license_image) $this->deleteFile($user->driver?->driving_license_image);
                }
                if ($request->file('car_image')) {
                    $data['car_image'] = $this->uploadFile($request->file('car_image'), 'driver/' . $user->id);
                    if($user->driver?->car_image) $this->deleteFile($user->driver?->car_image);
                }

                $user->driver()->updateOrCreate([], [
                    'driving_license_number' => $data['driving_license_number'],
                    'driving_license_expiry' => $data['driving_license_expiry'],
                    'driving_license_image'  => $data['driving_license_image'] ?? $user->driver->driving_license_image ?? '',
                    'car_model'              => $data['car_model'],
                    'car_make'               => $data['car_make'],
                    'car_year'               => $data['car_year'],
                    'car_color'              => $data['car_color'],
                    'car_plate_number'       => $data['car_plate_number'],
                    'car_image'              => $data['car_image'] ?? $user->driver->car_image ?? '',
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'User updated and role assigned successfully',
                'user' => $user->load('driver')
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('User update failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to update user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Soft delete a user
    public function destroy(User $user)
    {
        try {
            $user->delete();  // Soft delete
            return response()->json(['message' => 'User deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete user.', 'error' => $e->getMessage()], 500);
        }
    }

    // Restore a soft deleted permission
    public function restore($id)
    {
        try {
            $user = User::withTrashed()->findOrFail($id);
            $user->restore();
            return response()->json(['message' => 'User restored successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to restore user.', 'error' => $e->getMessage()], 500);
        }
    }

    // Permanently delete a user
    public function forceDelete($id)
    {
        try {
            $user = User::withTrashed()->findOrFail($id);
            $user->forceDelete();
            return response()->json(['message' => 'User permanently deleted.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to permanently delete user.', 'error' => $e->getMessage()], 500);
        }
    }

    public function getData(Request $request)
    {
        $query = User::excludeAuth()->orderBy('id', 'DESC');

        // Check if trashed is requested
        if ($request->filled('trashed') && $request->trashed == 'true') {
            $query = $query->onlyTrashed();
        }

        // Check if role filter is applied
        if (!empty($request->role) && $request->role !== 'null') {
            $role = $request->role;

            $query = $query->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role);
            });
        }

        $data = $query->get();

        $html = view('userrolepermission::components.user_row', compact('data'))->render();

        return response()->json(['html' => $html]);
    }


    public function stateGet(Country $country)
    {
        return State::where('country_id', $country->id)->get();
    }

    public function cityGet(State $state)
    {
        return City::where('state_id', $state->id)->get();
    }
}
