<?php

require_once __DIR__ . '/BaseModel.php';

class OrderProduct extends BaseModel
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'order_product');
    }
}
