<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDebtFund extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_debt_funds';

    /**
     * Fill the model with an array of attributes.
     *
     * @param  array
     */
    protected $fillable = ['month', 'year', 'amount'];
    
    /**
     * Get the user that owns the debt fund.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
