<?php
namespace Model;
use Core\Model;

class customer extends Model {
    protected $table = 'customer';
    protected $filltable = ['customer_name', 'customer_phone', 'customer_password', 'customer_address', 'create_at', 'update_at', 'province_id', 'distric_id', 'ward_id', 'customer_email'];
    protected $primarykey = 'customer_id';
}

?>