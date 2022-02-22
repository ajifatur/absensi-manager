<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobDutyResponsibility extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'job_duties_responsibilities';

    /**
     * Fill the model with an array of attributes.
     *
     * @param  array
     */
    protected $fillable = ['name'];
    
    /**
     * Get the position that owns the job duty & responsibility.
     */
    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }
}
