<?php

namespace App\Rules;

use App\Http\Traits\ApiResponseTrait;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StockValidation implements Rule
{
    use ApiResponseTrait;
    public $productId;
    public $message;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($productId)
    {
        $this->productId = $productId;
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
        $product = Product::where([ ['id', $this->productId], ['stock', '>=', $value] ])->first();
        if($product)
        {
           $validation = Validator::make( [ 'count' => request('count') ] ,[
                'count' => [new CartProductCountValidation($product->id)]
            ]);

            if($validation->fails())
            {
                $this->message = $validation->getMessageBag()->first('count');
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message == null ? 'Stock Not found' : $this->message;
    }
}
