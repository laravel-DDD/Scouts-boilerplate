<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as RoleBaseModel;

class Role extends RoleBaseModel
{
    use HasFactory;
}
