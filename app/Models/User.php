<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, LogsActivity;

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
        'roles',
        'updated_at',
        'email_verified_at'
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
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['role', 'photo'];

    /**
     * Log Attributes
     *
     * @var array
     */
    protected static $logAttributes = [
        'name',
        'email',
        'password',
        'role.name',
        'photo'
    ];

    /**
     * Scope a query to only include Search
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $value)
    {
        return $query->where('name', 'LIKE', '%' . $value . '%')->orWhere('email', 'LIKE', '%' . $value . '%');
    }

    /**
     * Get the Role
     *
     * @param  string  $value
     * @return string
     */
    public function getRoleAttribute($value)
    {
        return $this->roles->pluck('name');
    }

    /**
     * Get the Photo
     *
     * @param  string  $value
     * @return string
     */
    public function getPhotoAttribute($value)
    {
        return 'https://eu.ui-avatars.com/api/?name=' . urlencode($this->name) . '&size=250&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Forcing single guard permission to 'web'
     *
     * @return string
     */
    public function getDefaultGuardName(): string
    {
        return 'web';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty()->useLogName(config('app.name'));
    }
}
