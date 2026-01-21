<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'full_name',
        'email',
        'mobile',
        'password',
        'gender',
        'birthdate',
        'blood_type',
        'nationality',
        'addresses',
        'social_links',
        'media_gallery',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birthdate' => 'date',
            'addresses' => 'array',
            'social_links' => 'array',
            'media_gallery' => 'array',
        ];
    }

    /**
     * Get the user's age based on birthdate.
     */
    protected function age(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->birthdate) {
                    return null;
                }
                return Carbon::parse($this->birthdate)->age;
            }
        );
    }

    /**
     * Get the user's horoscope based on birthdate.
     */
    protected function horoscope(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->birthdate) {
                    return null;
                }

                $month = $this->birthdate->month;
                $day = $this->birthdate->day;

                if (($month == 3 && $day >= 21) || ($month == 4 && $day <= 19)) {
                    return 'Aries';
                } elseif (($month == 4 && $day >= 20) || ($month == 5 && $day <= 20)) {
                    return 'Taurus';
                } elseif (($month == 5 && $day >= 21) || ($month == 6 && $day <= 20)) {
                    return 'Gemini';
                } elseif (($month == 6 && $day >= 21) || ($month == 7 && $day <= 22)) {
                    return 'Cancer';
                } elseif (($month == 7 && $day >= 23) || ($month == 8 && $day <= 22)) {
                    return 'Leo';
                } elseif (($month == 8 && $day >= 23) || ($month == 9 && $day <= 22)) {
                    return 'Virgo';
                } elseif (($month == 9 && $day >= 23) || ($month == 10 && $day <= 22)) {
                    return 'Libra';
                } elseif (($month == 10 && $day >= 23) || ($month == 11 && $day <= 21)) {
                    return 'Scorpio';
                } elseif (($month == 11 && $day >= 22) || ($month == 12 && $day <= 21)) {
                    return 'Sagittarius';
                } elseif (($month == 12 && $day >= 22) || ($month == 1 && $day <= 19)) {
                    return 'Capricorn';
                } elseif (($month == 1 && $day >= 20) || ($month == 2 && $day <= 18)) {
                    return 'Aquarius';
                } else {
                    return 'Pisces';
                }
            }
        );
    }

    /**
     * Get the user's life stage based on age.
     */
    protected function lifeStage(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->birthdate) {
                    return null;
                }

                $age = Carbon::parse($this->birthdate)->age;

                if ($age >= 0 && $age <= 3) {
                    return 'Toddler';
                } elseif ($age >= 4 && $age <= 12) {
                    return 'Child';
                } elseif ($age >= 13 && $age <= 19) {
                    return 'Teenager';
                } elseif ($age >= 20 && $age <= 59) {
                    return 'Adult';
                } else {
                    return 'Senior';
                }
            }
        );
    }

    /**
     * Get the dependents for the user.
     */
    public function dependents(): HasMany
    {
        return $this->hasMany(UserRelationship::class, 'guardian_user_id');
    }

    /**
     * Get the guardians for the user.
     */
    public function guardians(): HasMany
    {
        return $this->hasMany(UserRelationship::class, 'dependent_user_id');
    }

    /**
     * Get the clubs owned by the user.
     */
    public function ownedClubs(): HasMany
    {
        return $this->hasMany(Tenant::class, 'owner_user_id');
    }

    /**
     * Get the clubs the user is a member of.
     */
    public function memberClubs(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'memberships')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    /**
     * Get the invoices where the user is the student.
     */
    public function studentInvoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'student_user_id');
    }

    /**
     * Get the invoices where the user is the payer.
     */
    public function payerInvoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'payer_user_id');
    }

    /**
     * Get the audit logs for the user.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'user_id');
    }

    /**
     * Get the real estate roles for the user.
     */
    public function realEstateRoles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'real_estate_role_user', 'user_id', 'role_id')
                    ->withTimestamps();
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        // Check by slug first (more common case)
        if ($this->realEstateRoles->where('slug', $role)->isNotEmpty()) {
            return true;
        }

        // Also check by name for flexibility
        if ($this->realEstateRoles->where('name', $role)->isNotEmpty()) {
            return true;
        }

        return false;
    }

    /**
     * Check if the user has a specific permission through their roles.
     */
    public function hasPermission(string $permission): bool
    {
        // Super admin has all permissions
        if ($this->hasRole(Role::ROLE_SUPER_ADMIN)) {
            return true;
        }

        // Check through roles
        foreach ($this->realEstateRoles as $role) {
            if ($role->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the user is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole(Role::ROLE_SUPER_ADMIN);
    }

    /**
     * Assign a role to the user.
     */
    public function assignRole(Role $role): void
    {
        $this->realEstateRoles()->syncWithoutDetaching($role);
    }

    /**
     * Remove a role from the user.
     */
    public function removeRole(Role $role): void
    {
        $this->realEstateRoles()->detach($role);
    }

    /**
     * Sync user's roles.
     */
    public function syncRoles(array $roles): void
    {
        $this->realEstateRoles()->sync($roles);
    }
}
