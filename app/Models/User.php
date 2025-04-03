<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class User extends Authenticatable implements FilamentUser, HasTenants
{
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
        ];
    }

    /**
     * Relationship: A user may belong to many stores.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function stores()
    {
        return $this->belongsToMany(Store::class)
            ->withPivot(['role']);
    }

    /**
     * Determine if the user can access Filament.
     * You can add extra logic here if you want to limit access.
     *
     * @return bool
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // You can implement any custom logic here, e.g.:
        // return $this->hasVerifiedEmail();
        return true; // Or your own condition
    }

    /**
     * Return a collection of tenants (stores) that this user has access to.
     *
     * @param  \Filament\Panel  $panel
     * @return \Illuminate\Support\Collection
     */
    public function getTenants(Panel $panel): Collection
    {
        return $this->stores;
    }

    /**
     * Ensure that the user cannot guess a store ID/slug
     * for a store that they do not belong to.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $tenant
     * @return bool
     */
    public function canAccessTenant(\Illuminate\Database\Eloquent\Model $tenant): bool
    {
        return $this->stores()
            ->whereKey($tenant->getKey())
            ->exists();
    }
}
