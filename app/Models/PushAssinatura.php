<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushAssinatura extends Model
{
    protected $table = 'tb_push_assinatura';

    protected $primaryKey = 'pus_id';

    protected $guarded = ['pus_id'];

    protected $hidden = ['pus_id', 'ace_id'];
}
