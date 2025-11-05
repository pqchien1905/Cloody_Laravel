<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        'email',
        'password',
        'is_admin',
        'avatar',
        'phone',
        'address',
        'bio',
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
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Get all files owned by the user.
     */
    public function files()
    {
        return $this->hasMany(File::class);
    }

    /**
     * Get all folders owned by the user.
     */
    public function folders()
    {
        return $this->hasMany(Folder::class);
    }

    /**
     * Get files shared by this user.
     */
    public function sharedFiles()
    {
        return $this->hasMany(FileShare::class, 'shared_by');
    }

    /**
     * Get files shared with this user.
     */
    public function receivedShares()
    {
        return $this->hasMany(FileShare::class, 'shared_with');
    }
}
