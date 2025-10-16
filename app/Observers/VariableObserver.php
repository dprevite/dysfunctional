<?php

namespace App\Observers;

use App\Models\Variable;
use Illuminate\Support\Facades\Crypt;

class VariableObserver
{
    /**
     * Handle the Variable "creating" event.
     */
    public function creating(Variable $variable): void
    {
        if ($variable->is_secret && $variable->value) {
            $variable->value = Crypt::encryptString($variable->value);
        }
    }

    /**
     * Handle the Variable "updating" event.
     */
    public function updating(Variable $variable): void
    {
        if ($variable->isDirty('value') && $variable->is_secret && $variable->value) {
            // Check if the value is already encrypted by trying to decrypt it
            try {
                Crypt::decryptString($variable->value);
                // If we get here, it's already encrypted, don't re-encrypt
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                // Not encrypted, so encrypt it
                $variable->value = Crypt::encryptString($variable->value);
            }
        }
    }

    /**
     * Handle the Variable "retrieved" event.
     */
    public function retrieved(Variable $variable): void
    {
        if ($variable->is_secret && $variable->value) {
            try {
                $variable->value = Crypt::decryptString($variable->value);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                // Value is not encrypted, leave it as is
            }
        }
    }
}
