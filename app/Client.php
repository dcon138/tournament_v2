<?php

namespace App;

use App\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends BaseModel {
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['client_group_id', 'name', 'short_name', 'abn', 'primary_contact_id', 'address', 'address2',
                            'state_id', 'suburb', 'postcode', 'bank_details'];

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

    protected $unconventionalForeignKeys = [
        'primary_contact_id' => 'users',
    ];

    public function client_group()
    {
        return $this->belongsTo('App\ClientGroup');
    }

    public function primary_contact()
    {
        return $this->belongsTo('App\User', 'primary_contact_id');
    }

    public function state()
    {
        return $this->belongsTo('App\State');
    }

    public function users()
    {
        return $this->belongsToMany('App\User', 'users_clients');
    }
}