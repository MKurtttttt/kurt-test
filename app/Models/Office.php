<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    // Explicitly specify the table name
    protected $table = 'sharepoint_offices';

    protected $fillable = ['name', 'sharepoint_department_id'];

    public function department()
    {
        return $this->belongsTo(SharepointDepartment::class, 'sharepoint_department_id');
    }

    public function links()
    {
        return $this->hasMany(SharepointLink::class, 'sharepoint_office_id');
    }
}
