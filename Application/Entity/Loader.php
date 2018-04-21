<?php

use Application\Entity\{Customer, Product, Purchase,Profile};

Customer::init();
Product::init('products');
Purchase::init('purchases');
Profile::init();