<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{

    public static function active()
    {
      return static::where('active', 1)->get();
    }

    public function isActive()
    {
      return false;
    }

    public function isCompleted()
    {
      return true;
    }

}
