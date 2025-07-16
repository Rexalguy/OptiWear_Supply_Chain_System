<?php
public class CartObject
{
public $id;
public $name;
public $price;
public $quantity;
public $bale_size;

public function __construct($id, $name, $price, $quantity, $bale_size = null)
{
$this->id = $id;
$this->name = $name;
$this->price = $price;
$this->quantity = $quantity;
$this->bale_size = $bale_size;
}
}