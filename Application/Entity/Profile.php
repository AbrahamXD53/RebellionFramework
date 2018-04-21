<?php 

namespace Application\Entity;

class Profile extends Model
{
    protected static $protectedFields=['postal_code'];

    public function customer(){
        return $this->hasOne(Customer::class);
    }
   
}