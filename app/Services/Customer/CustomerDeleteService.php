<?php

namespace App\Services\Customer;

use App\Models\User;
use App\Models\Authenticate;

class CustomerDeleteService
{
    public function deleteCustomer($id)
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

            // Soft delete user
            $user->delete();

            // Also delete related authenticate record
            Authenticate::where('entity_id', $id)
                ->where('entity_type', User::class)
                ->delete();

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
