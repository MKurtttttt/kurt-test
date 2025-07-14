<?php
// This file is for the Departments of the SharePoint sites
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SharepointDepartment extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function offices() {
        return $this->hasMany(Office::class, 'sharepoint_department_id');
    }

    public function links() {
        return $this->hasMany(SharepointLink::class, 'sharepoint_department_id');
    }
}