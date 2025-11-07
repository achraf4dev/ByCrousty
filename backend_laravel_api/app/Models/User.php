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
        'points',
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
        'points' => 'integer',
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

    /**
     * Get the points history for this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pointsHistory()
    {
        return $this->hasMany(PointsHistory::class, 'user_id');
    }

    /**
     * Get the points awarded by this admin.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function awardedPoints()
    {
        return $this->hasMany(PointsHistory::class, 'admin_id');
    }

    /**
     * Add points to user and log the transaction.
     *
     * @param int $points
     * @param int $adminId
     * @param string $description
     * @param string|null $qrCodeData
     * @return bool
     */
    public function addPoints($points, $adminId, $description = null, $qrCodeData = null)
    {
        // Update user points
        $this->increment('points', $points);

        // Log the transaction
        PointsHistory::create([
            'user_id' => $this->id,
            'admin_id' => $adminId,
            'points' => $points,
            'type' => 'manual',
            'description' => $description,
            'qr_code_data' => $qrCodeData,
        ]);

        return true;
    }

    /**
     * Get total points earned this month.
     *
     * @return int
     */
    public function getPointsThisMonthAttribute()
    {
        return $this->pointsHistory()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('points');
    }

    /**
     * Get recent points history (last 10 transactions).
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentPointsHistoryAttribute()
    {
        return $this->pointsHistory()
            ->with('admin')
            ->latest()
            ->take(10)
            ->get();
    }

}
