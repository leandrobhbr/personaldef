<?php

namespace App\Models;

use App\Models\Concerns\TemHashid;
use Illuminate\Database\Eloquent\Model;

class Cadencia extends Model
{
    use TemHashid;

    protected $table = 'tb_cadencia';

    protected $primaryKey = 'cad_id';

    public $timestamps = false;

    protected $guarded = ['cad_id'];

    // nas APIs só sai o hashid de 6 caracteres, nunca o id numérico
    protected $hidden = ['cad_id'];

    protected $appends = ['cad_hashid'];

    public function getCadHashidAttribute(): ?string
    {
        return $this->hashidDaChave();
    }
}
