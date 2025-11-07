<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'full_name',
        'username',
        'email',
        'password',
        'role',
        'birthday',
        'street_address',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country',
        'phone_code',
        'phone_number',
        'email_verified_at',
        'qr_code_data',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'qr_code_data', // Hide the raw QR code data for security
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['qr_code_url'];

    /**
     * Check if the user is an admin.
     *
     * @return bool
     */
    public function getIsAdminAttribute()
    {
        return $this->role === 'admin';
    }

    /**
     * Scope to get only admin users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Get the QR code URL for the user.
     *
     * @return string|null
     */
    public function getQrCodeUrlAttribute()
    {
        if (empty($this->qr_code_data)) {
            return null;
        }
        
        // Return a URL that points to the existing QR code endpoint
        return url('/api/v1/users/my-qr-code');
    }

}
