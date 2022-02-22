<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobAuthority extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'job_authorities';

    /**
     * Fill the model with an array of attributes.
     *
     * @param  array
     */
    protected $fillable = ['name'];
    
    /**
     * Get the position that owns the job authority.
     */
    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }
}
