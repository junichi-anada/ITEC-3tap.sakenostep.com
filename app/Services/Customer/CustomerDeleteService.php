<?php

namespace App\Services\Customer;

use App\Models\User;
use App\Models\Authenticate;
use App\Models\LineUser;
use Illuminate\Support\Facades\DB;

class CustomerDeleteService
{
    public function deleteCustomer(User $user, $auth)
    {
        try {
            DB::beginTransaction();

            // Delete LINE user if exists
            LineUser::where('user_id', $user->id)->delete();

            // Soft delete authenticate record
            Authenticate::where('entity_id', $user->id)
                ->where('entity_type', User::class)
                ->get()
                ->each(function ($auth) {
                    $auth->delete();
                });

            // Delete user
            $user->delete();

            DB::commit();

            return [
                'message' => 'success'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'message' => 'error',
                'reason' => $e->getMessage()
            ];
        }
    }
}
