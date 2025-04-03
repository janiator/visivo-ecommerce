<?php

namespace App\Models;

use Filament\Panel;
use Illuminate\Support\Collection;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\HasTenants;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser, HasTenants
{
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['password', 'remember_token'];

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
     * Get the stores.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function stores()
    {
        return $this->belongsToMany(Store::class)->withPivot('role');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // You can implement any custom logic here, e.g.:
        // return $this->hasVerifiedEmail();
        return true; // Or your own condition
    }

    public function getTenants(Panel $panel): Collection
    {
        return $this->stores;
    }

    public function canAccessTenant(
        \Illuminate\Database\Eloquent\Model $tenant
    ): bool {
        return $this->stores()
            ->whereKey($tenant->getKey())
            ->exists();
    }
}
