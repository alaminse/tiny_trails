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
            $roles = Role::where('name', 'driver')->get();
        } elseif (request()->routeIs('admin.parents.index')) {
            $this->roleName = 'parent';
            $roles = Role::where('name', 'parent')->get();
        } else {
            $this->roleName = 'BOH';
            $roles = Role::where('name', 'LIKE', 'boh_%')->get();
        }

        $countries = Country::where('status', 'active')->get();

        return view('userrolepermission::index', [
                    'countries' => $countries,
                    'roles'     => $roles,
                    'roleName'  => $this->roleName,
                ]);

    }

    public function store(UserRequest $request)
    {

        // return $request;
        // try {
        //     DB::beginTransaction();

            $data = $request->validated(); // ✅ safer than manually pulling fields

            $allFiles = $request->allFiles();

            // Log for debugging: Check what files we received
            Log::info('Files received at the start of the request:', array_keys($allFiles));

            // Handle the main photo using the files array, NOT the request object
            if (isset($allFiles['photo']) && $allFiles['photo'] instanceof \Illuminate\Http\UploadedFile) {
                $data['photo'] = $this->uploadFile($allFiles['photo'], 'user');
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
                $this->handleDriverData($user, $data, false, $allFiles);
            }

            DB::commit();

            return response()->json([
                'message' => 'User created and role assigned successfully',
                'user' => $user->load('driver') // if applicable
            ], 201);

        // } catch (Exception $e) {
        //     DB::rollBack();
        //     Log::error('User creation failed: ' . $e->getMessage());

        //     return response()->json([
        //         'message' => 'Failed to create user',
        //         'error' => $e->getMessage()
        //     ], 500);
        // }
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
                // --- License Details ---
                'driving_license_number'  => $driver->driving_license_number,
                'licence_card_number'     => $driver->licence_card_number,
                'licence_type'            => $driver->licence_type,
                'driving_license_expiry'  => $driver->driving_license_expiry,
                'driving_license_image'   => $driver->driving_license_image ? asset($driver->driving_license_image) : null,

                // --- License Address ---
                'licence_address_line_1' => $driver->licence_address_line_1,
                'licence_address_line_2' => $driver->licence_address_line_2,
                'licence_city'           => $driver->licence_city,
                'licence_state'          => $driver->licence_state,
                'licence_postal_code'    => $driver->licence_postal_code,
                'licence_country'        => $driver->licence_country,

                // --- Car Details ---
                'car_make'         => $driver->car_make,
                'car_model'        => $driver->car_model,
                'car_year'         => $driver->car_year,
                'car_color'        => $driver->car_color,
                'car_plate_number' => $driver->car_plate_number,
                'car_image'        => $driver->car_image ? asset($driver->car_image) : null,

                // --- Other Qualifications & Documents ---
                'wwc_card_number'        => $driver->wwc_card_number,
                'wwc_expiry_date'        => $driver->wwc_expiry_date,
                'wwc_card_image'         => $driver->wwc_card_image ? asset($driver->wwc_card_image) : null,
                'police_clearance_ref'   => $driver->police_clearance_ref,
                'police_clearance_image' => $driver->police_clearance_image ? asset($driver->police_clearance_image) : null,
                'other_qualifications'   => $driver->other_qualifications,
                'face_image'             => $driver->face_image ? asset($driver->face_image) : null,

                // --- Driver Status ---
                'is_verified'   => $driver->is_verified,
                'driver_status' => $driver->driver_status,
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
                // --- License Details ---
                'driving_license_number'  => $driver->driving_license_number,
                'licence_card_number'     => $driver->licence_card_number,
                'licence_type'            => $driver->licence_type,
                'driving_license_expiry'  => $driver->driving_license_expiry,
                'driving_license_image'   => $driver->driving_license_image ? asset($driver->driving_license_image) : null,

                // --- License Address ---
                'licence_address_line_1' => $driver->licence_address_line_1,
                'licence_address_line_2' => $driver->licence_address_line_2,
                'licence_city'           => $driver->licence_city,
                'licence_state'          => $driver->licence_state,
                'licence_postal_code'    => $driver->licence_postal_code,
                'licence_country'        => $driver->licence_country,

                // --- Car Details ---
                'car_make'         => $driver->car_make,
                'car_model'        => $driver->car_model,
                'car_year'         => $driver->car_year,
                'car_color'        => $driver->car_color,
                'car_plate_number' => $driver->car_plate_number,
                'car_image'        => $driver->car_image ? asset($driver->car_image) : null,

                // --- Other Qualifications & Documents ---
                'wwc_card_number'        => $driver->wwc_card_number,
                'wwc_expiry_date'        => $driver->wwc_expiry_date,
                'wwc_card_image'         => $driver->wwc_card_image ? asset($driver->wwc_card_image) : null,
                'police_clearance_ref'   => $driver->police_clearance_ref,
                'police_clearance_image' => $driver->police_clearance_image ? asset($driver->police_clearance_image) : null,
                'other_qualifications'   => $driver->other_qualifications,
                'face_image'             => $driver->face_image ? asset($driver->face_image) : null,

                // --- Driver Status ---
                'is_verified'   => $driver->is_verified,
                'driver_status' => $driver->driver_status,
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
            $allFiles = $request->allFiles();
            // Upload new profile photo if provided
            if ($request->file('photo')) {
                $data['photo'] = $this->uploadFile($request->file('photo'), 'user');
                if($user->photo) $this->deleteFile($user->photo);
            }

             // Handle the main photo using the files array, NOT the request object
            if (isset($allFiles['photo']) && $allFiles['photo'] instanceof \Illuminate\Http\UploadedFile) {
                $data['photo'] = $this->uploadFile($allFiles['photo'], 'user');
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
                $this->handleDriverData($user, $data, true, $allFiles);

                // if ($request->file('driving_license_image')) {
                //     $data['driving_license_image'] = $this->uploadFile($request->file('driving_license_image'), 'driver/' . $user->id);
                //     if($user->driver?->driving_license_image) $this->deleteFile($user->driver?->driving_license_image);
                // }
                // if ($request->file('car_image')) {
                //     $data['car_image'] = $this->uploadFile($request->file('car_image'), 'driver/' . $user->id);
                //     if($user->driver?->car_image) $this->deleteFile($user->driver?->car_image);
                // }

                // $user->driver()->updateOrCreate([], [
                //     'driving_license_number' => $data['driving_license_number'],
                //     'driving_license_expiry' => $data['driving_license_expiry'],
                //     'driving_license_image'  => $data['driving_license_image'] ?? $user->driver->driving_license_image ?? '',
                //     'car_model'              => $data['car_model'],
                //     'car_make'               => $data['car_make'],
                //     'car_year'               => $data['car_year'],
                //     'car_color'              => $data['car_color'],
                //     'car_plate_number'       => $data['car_plate_number'],
                //     'car_image'              => $data['car_image'] ?? $user->driver->car_image ?? '',
                // ]);
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

    /**
     * @param \App\Models\User $user
     * @param array $data
     * @param bool $isUpdate
     * @param \Illuminate\Http\UploadedFile[] $files // The array of files from the controller
     */
    private function handleDriverData(User $user, array $data, bool $isUpdate = false, array $files)
    {
        $driverData = [];
        $driverFields = [
            'driving_license_number', 'licence_card_number', 'licence_type', 'driving_license_expiry',
            'car_make', 'car_model', 'car_year', 'car_color', 'car_plate_number',
            'wwc_card_number', 'wwc_expiry_date', 'police_clearance_ref', 'other_qualifications',
            'is_verified', 'driver_status', 'licence_address_line_1', 'licence_address_line_2',
            'licence_city', 'licence_state', 'licence_postal_code', 'licence_country'
        ];

        foreach ($driverFields as $field) {
            if (array_key_exists($field, $data)) {
                $driverData[$field] = $data[$field];
            }
        }

        // Handle driver-specific file uploads from the PASSED ARRAY
        $driverImages = ['driving_license_image', 'car_image', 'wwc_card_image', 'police_clearance_image', 'face_image'];

        foreach ($driverImages as $image) {
            // Check if the file exists in the array we passed from the controller
            if (isset($files[$image]) && $files[$image] instanceof \Illuminate\Http\UploadedFile) {
                $file = $files[$image];

                // Log for debugging
                Log::info("Processing driver image: $image", [
                    'original_name' => $file->getClientOriginalName(),
                    'temp_path' => $file->getPathname(),
                    'exists' => file_exists($file->getPathname())
                ]);

                // If updating, delete the old file first
                if ($isUpdate && $user->driver?->$image) {
                    $this->deleteFile($user->driver->$image);
                }

                // Upload the new file
                $driverData[$image] = $this->uploadFile($file, 'driver/' . $user->id);
            }
        }

        if ($isUpdate) {
            $user->driver()->update($driverData);
        } else {
            $user->driver()->create($driverData);
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

            if($role == 'BOH')
            {
                $query = $query->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', 'LIKE', 'BOH_%');
                });

            } else {
                $query = $query->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role);
                });
            }
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
