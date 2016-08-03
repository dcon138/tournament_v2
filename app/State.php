<?php

namespace App;

use App\Traits\UuidModel;
use App\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class State extends BaseModel {
    use UuidModel, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'short_name'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['id'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}