<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $fillable = array('name', 'label', 'description');

    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }
    /**
     * Determine if the permission belongs to the role.
     *
     * @param  mixed $role
     * @return boolean
     */
    public function inRole($role): bool
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }
        return !!$role->intersect($this->roles)->count();
    }
}
