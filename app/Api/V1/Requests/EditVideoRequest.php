<?php

namespace App\Api\V1\Requests;

use Config;
use Dingo\Api\Http\FormRequest;

class EditVideoRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('boilerplate.edit_video.validation_rules');
    }

    public function authorize()
    {
        return true;
    }
}
