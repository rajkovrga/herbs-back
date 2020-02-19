<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHerb extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'desc' => 'required|min:20',
            'srb_name' => 'required|max:100|min:7',
            'lat_name' => 'required|max:100|min:7',
            'toxic' => 'required|boolean',
            'endangered' => 'required|boolean',
            'pickpart_id' => 'required',
            'period_id' => 'required',
            'file' => 'mimes:png,gif,jpg,jpeg|max:3000'
        ];
    }

    public function messages()
    {
        return [
            'image.mimes' => 'Nije dobar format slike',
            'image.max' => 'Slika prevelika'
        ];
    }
}
