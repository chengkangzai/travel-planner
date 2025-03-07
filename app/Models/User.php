<?php

namespace App\Models;

use App\Models\Pivot\ExpenseUser;
use App\Models\Pivot\UserTeam;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser, HasTenants
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'user_team', 'user_id', 'team_id')
            ->using(UserTeam::class);
    }

    public function getTenants(Panel $panel): Collection
    {
        return $this->teams;
    }

    public function canAccessTenant(Team|Model $tenant): bool
    {
        return $this->teams()->whereKey($tenant)->exists();
    }

    public function expenses(): BelongsToMany
    {
        return $this->belongsToMany(Expenses::class, 'expense_user', 'user_id', 'expenses_id')
            ->using(ExpenseUser::class);
    }
}
