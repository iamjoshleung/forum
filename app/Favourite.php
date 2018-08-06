<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\RecordsActivity;

class Favourite extends Model
{
    use RecordsActivity;

    protected $guarded = [];

    /**
     * 
     * 
     * @return 
     */
    public function favourited() {
        return $this->morphTo();
    }
}
