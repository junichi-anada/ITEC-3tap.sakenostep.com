<?php

namespace App\Services\Customer\Exceptions;

use Exception;

class CustomerException extends Exception
{
    public static function notFound(int $id): self
    {
        return new self("Customer with ID {$id} not found");
    }

    public static function invalidData(string $message): self
    {
        return new self("Invalid customer data: {$message}");
    }

    public static function createFailed(string $reason): self
    {
        return new self("Failed to create customer: {$reason}");
    }

    public static function updateFailed(int $id, string $reason): self
    {
        return new self("Failed to update customer {$id}: {$reason}");
    }

    public static function deleteFailed(int $id, string $reason): self
    {
        return new self("Failed to delete customer {$id}: {$reason}");
    }

    public static function importFailed(string $reason): self
    {
        return new self("Customer import failed: {$reason}");
    }

    public static function restoreFailed(int $id, string $reason): self
    {
        return new self("Failed to restore customer {$id}: {$reason}");
    }

    public static function invalidSearchCriteria(string $reason): self
    {
        return new self("Invalid search criteria: {$reason}");
    }
}
