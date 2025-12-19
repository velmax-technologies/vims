<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'contact_person',
        'kra_pin',
        'bank_account',
        'notes',
    ];

    /**
     * Get the suppliers' name.
     */
    public function getNameAttribute($value)
    {
        return ucfirst($value);
    }

    /**
     * Get the suppliers' email.
     */
    public function getEmailAttribute($value)
    {
        return strtolower($value);
    }

    /**
     * Get the suppliers' phone number.
     */
    public function getPhoneAttribute($value)
    {
        return preg_replace('/\D/', '', $value); // Remove non-numeric characters
    }

    /**
     * Get the suppliers' address.
     */
    public function getAddressAttribute($value)
    {
        return trim($value);
    }

    /**
     * Get the suppliers' contact person.
     */
    public function getContactPersonAttribute($value)
    {
        return ucfirst($value);
    }

    /**
     * Get the suppliers' KRA PIN.
     */
    public function getKraPinAttribute($value)
    {
        return strtoupper($value);
    }

    /**
     * Get the suppliers' bank account.
     */
    public function getBankAccountAttribute($value)
    {
        return strtoupper($value);
    }

    /**
     * Get the suppliers' notes.
     */
    public function getNotesAttribute($value)
    {
        return trim($value);
    }

}
