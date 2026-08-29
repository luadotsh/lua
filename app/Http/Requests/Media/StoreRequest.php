<?php

declare(strict_types=1);

namespace App\Http\Requests\Media;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'media' => ['required', 'file'],
            'collection' => ['required'],
            'model' => ['required'],
            'model_id' => ['required'],
            'visibility' => ['required', 'in:public,private'],
        ];
    }
}
