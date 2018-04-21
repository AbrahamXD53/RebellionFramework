<?php

namespace Application\Entity;

class Customer extends Model
{
    protected static $protectedFields = ['password','security_question'];
    
    public function purchases(){
        return $this->hasMany(Purchase::class);
    }
    public function profile(){
        return $this->belongsTo(Profile::class);
    }
}