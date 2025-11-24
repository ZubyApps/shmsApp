<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'description'       => $this->description,
            'expenseCategory'   => $this->expense_category_id,
            'amount'            => $this->amount,
            'givenTo'           => $this->given_to,
            'approvedBy'        => $this->approved_by,
            'comment'           => $this->comment,
            'payMethod'         => $this->pay_method_id,
        ];
    }
}
