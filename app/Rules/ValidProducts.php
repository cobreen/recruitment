<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ValidProducts implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        foreach ($value as $id => $product) {
            $validator = Validator::make($product, [
                "name" => ["required"],
                "price" => ["required", "numeric"],
                "category" => ["required"],
                "image" => ["required", "active_url"],
            ]);
            if ($validator->fails()) {
                throw new HttpResponseException(response()->json($validator->errors(), 422));
           }
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid products array';
    }
}
