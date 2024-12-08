<?php

namespace App\Services\Customer;

use App\Models\User;

class CustomerUpdateService
{
    public function updateCustomer($id, $data)
    {
        try {
            // Find user
            $user = User::find($id);
            if (!$user) {
                return [
                    'message' => 'error',
                    'reason' => 'User not found'
                ];
            }

            // Update user data
            $user->name = $data['name'];
            $user->postal_code = $data['postal_code'];
            $user->phone = $data['phone'];
            $user->address = $data['address'];
            $user->save();

            return [
                'message' => 'success'
            ];
        } catch (\Exception $e) {
            return [
                'message' => 'error',
                'reason' => $e->getMessage()
            ];
        }
    }
}
