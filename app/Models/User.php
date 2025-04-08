<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Store;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
     * Get the casts for the model.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
     * Relationship: A user may belong to many stores.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(Store::class)
            ->using(StoreUser::class)
            ->withPivot(['role']);
    }

    /**
     * Determine if the user can access Filament.
     *
     * @param \Filament\Panel $panel
     * @return bool
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    /**
     * Return a collection of stores (tenants) that the user has access to.
     *
     * For the super-admin (user ID 1), return all stores.
     *
     * @param \Filament\Panel $panel
     * @return \Illuminate\Support\Collection
     */
    public function getTenants(Panel $panel): Collection
    {
        if ($this->getKey() === 1) {
            // Super-admin: return all tenants, regardless of pivot data.
            return Store::all();
        }

        return $this->stores;
    }

    /**
     * Check if the user can access a given store.
     *
     * For the super-admin (user ID 1), always return true.
     *
     * @param \Illuminate\Database\Eloquent\Model $tenant
     * @return bool
     */
    public function canAccessTenant(\Illuminate\Database\Eloquent\Model $tenant): bool
    {
        if ($this->getKey() === 1) {
            return true;
        }

        return $this->stores()->whereKey($tenant->getKey())->exists();
    }
}
