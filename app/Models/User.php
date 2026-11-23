<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

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
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'role_id',
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
            'is_active' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function member(): HasOne
    {
        return $this->hasOne(Member::class, 'user_id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(UserApproval::class, 'evaluator_id');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function circulations(): HasMany
    {
        return $this->hasMany(Circulation::class);
    }

    public function sanctions(): HasMany
    {
        return $this->hasMany(Sanction::class);
    }

    public function accountDeactivationReason(): ?string
    {
        $observation = $this->circulations()
            ->whereHas('status', fn ($query) => $query->where('name', Circulation::STATUS_LOST))
            ->latest('id')
            ->value('observation');

        if (! $observation || ! Str::contains($observation, 'Préstamo marcado como perdido:')) {
            return null;
        }

        return trim(Str::after($observation, 'Préstamo marcado como perdido:')) ?: null;
    }

    public function hasPermission(string $permission): bool
    {
        if (!$this->role || !$this->role->is_active) {
            return false;
        }

        return $this->role
            ->permissions()
            ->where('name', $permission)
            ->exists();
    }
}
