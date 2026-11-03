<?php

namespace App\Models;

use App\Models\Concerns\TemHashid;
use Illuminate\Database\Eloquent\Model;

class Metodo extends Model
{
    use TemHashid;

    protected $table = 'tb_metodo';

    protected $primaryKey = 'met_id';

    public $timestamps = false;

    protected $guarded = ['met_id'];

    // nas APIs só sai o hashid de 6 caracteres, nunca o id numérico
    protected $hidden = ['met_id'];

    protected $appends = ['met_hashid'];

    public function getMetHashidAttribute(): ?string
    {
        return $this->hashidDaChave();
    }
}
