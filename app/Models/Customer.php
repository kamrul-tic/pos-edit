<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Customer extends Model
{
    protected $fillable =[
        "customer_group_id", "user_id", "name", "company_name",
        "email", "phone_number", "tax_no", "address", "city",
        "state", "postal_code", "country", "points", "deposit", "expense", "wishlist", "is_active"
    ];
    
    protected $appends = ['total_due'];

public function getTotalDueAttribute()
{
    // Total sales for this customer
    $saleData = DB::table('sales')
        ->where('customer_id', $this->id)
        ->selectRaw('SUM(grand_total) as grand_total, SUM(paid_amount) as paid_amount')
        ->first();

    // Returned amount for this customer
    $returned_amount = DB::table('sales')
        ->join('returns', 'sales.id', '=', 'returns.sale_id')
        ->where('sales.customer_id', $this->id)
        ->where('sales.payment_status', '!=', 4)
        ->sum('returns.grand_total');

    $grand_total = $saleData->grand_total ?? 0;
    $paid_amount = $saleData->paid_amount ?? 0;

    return ($grand_total - $returned_amount) - $paid_amount;
}


    public function customerGroup()
    {
        return $this->belongsTo('App\Models\CustomerGroup');
    }

    public function user()
    {
    	return $this->belongsTo('App\Models\User');
    }

    public function discountPlans()
    {
        return $this->belongsToMany('App\Models\DiscountPlan', 'discount_plan_customers');
    }
}
