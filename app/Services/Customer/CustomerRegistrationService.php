<?php

namespace App\Services\Customer;

use App\Models\User;
use App\Models\Authenticate;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class CustomerRegistrationService
{
    public function registCustomer($request, $auth)
    {
        try {
            // Create user
            $user = new User();
            $user->name = $request->name;
            $user->postal_code = $request->postal_code;
            $user->phone = $request->phone;
            $user->address = $request->address;
            $user->save();

            // Generate login credentials
            $login_code = $request->user_code;
            $password = Str::random(8);

            // Create authenticate record
            $authenticate = new Authenticate();
            $authenticate->login_code = $login_code;
            $authenticate->password = Hash::make($password);
            $authenticate->entity_id = $user->id;
            $authenticate->entity_type = User::class;
            $authenticate->save();

            return [
                'message' => 'success',
                'login_code' => $login_code,
                'password' => $password
            ];
        } catch (\Exception $e) {
            return [
                'message' => 'error',
                'reason' => $e->getMessage()
            ];
        }
    }
}
