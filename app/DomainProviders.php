<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DomainProviders extends Model
{
    protected $table = '_domains_provider';

    protected $fillable = ['id','name','username','user_id','api_key','status'];


}
