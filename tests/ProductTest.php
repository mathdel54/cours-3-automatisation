<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Entity\Product;
use App\Entity\Wallet;

class ProductTest extends TestCase
{
    protected Product $product1;
    protected Product $product2;

    protected function setUp(): void
    {
        $this->product1 = new Product('Twitter Blue', ['EUR' => 8.99, 'USD' => 10.00], 'tech');
        $this->product2 = new Product('Pomme', ['EUR' => 1.00], 'food');
    }

    public function testGetName()
    {
        $this->assertEquals('Twitter Blue', $this->product1->getName());
    }

    public function testGetPrices()
    {
        $this->assertEquals(['EUR' => 8.99, 'USD' => 10.00], $this->product1->getPrices());
    }

    public function testSetPrices()
    {
        $this->product1->setPrices(['EUR' => 9.99, 'USD' => 11.00]);
        $this->assertEquals(['EUR' => 9.99, 'USD' => 11.00], $this->product1->getPrices());
    }

    public function testSetPricesInvalidCurrency()
    {
        $this->product1->setPrices(['GBP' => 9.99, 'USD' => 11.00]);
        $this->assertEquals(['EUR' => 8.99, 'USD' => 11.00], $this->product1->getPrices());
    }

    public function testSetPricesNegativeValue()
    {
        $this->product1->setPrices(['EUR' => -9.99, 'USD' => 11.00]);
        $this->assertEquals(['EUR' => 8.99, 'USD' => 11.00], $this->product1->getPrices());
    }

    public function testGetType()
    {
        $this->assertEquals('tech', $this->product1->getType());
    }

    public function testSetTypeInvalidType()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid type');
        $this->product1->setType('invalid_type');
    }

    public function testGetTVA()
    {
        $this->assertEquals(0.2, $this->product1->getTVA());

        $foodProduct = new Product('Apple', ['EUR' => 1.00], 'food');
        $this->assertEquals(0.1, $foodProduct->getTVA());
    }

    public function testListCurrencies()
    {
        $this->assertEquals(['EUR', 'USD'], $this->product1->listCurrencies());
    }

    public function testGetPrice()
    {
        $this->assertEquals(8.99, $this->product1->getPrice('EUR'));
        $this->assertEquals(10.00, $this->product1->getPrice('USD'));
    }

    public function testGetPriceUnavailableCurrency()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Currency not available for this product');
        $this->product2->getPrice('USD');
    }

    public function testGetPriceInvalidCurrency()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid currency');
        $this->product1->getPrice('GBP');
    }
}