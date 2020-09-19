<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    protected $table='servers';

    protected $fillable = [
        'id',
        'name',
        'ip',
        'username',
        'password',
        'status'
    ];


}
