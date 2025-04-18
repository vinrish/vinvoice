<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $fillable = array('name','status', 'label', 'description');

    public function permissions(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function givePermissionTo(Permission $permission): Model
    {
        return $this->permissions()->save($permission);
    }
    /**
     * Determine if the user may perform the given permission.
     *
     * @param  Permission $permission
     * @return boolean
     */
    public function hasPermission(Permission $permission, User $user): bool
    {
        return $this->hasRole($permission->roles);
    }
    /**
     * Determine if the role has the given permission.
     *
     * @param  mixed $permission
     * @return boolean
     */
    public function inRole($permission): bool
    {
        if (is_string($permission)) {
            return $this->permissions->contains('name', $permission);
        }
        return !!$permission->intersect($this->permissions)->count();
    }
}
