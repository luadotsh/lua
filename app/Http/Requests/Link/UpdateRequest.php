<?php

declare(strict_types=1);

namespace App\Http\Requests\Link;

use App\Actions\Link\CreateLink;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // The API middleware merges the token's workspace onto the request;
        // on the web it is the one the user has selected.
        $workspace = $this->workspace ?? $this->user()?->currentWorkspace;

        return CreateLink::rules($workspace, $this->all(), $this->route('id'));
    }
}
