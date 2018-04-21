<?php

namespace Application\Entity;

class Purchase extends Model{

    protected static $protectedFields=['customer_id','product_id'];
    
    public function product(){
        return $this->belongsTo(Product::class,'product_id','product');
    }
    public function customer(){
        return $this->belongsTo(Customer::class);
    }
}