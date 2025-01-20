<?php 

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Entity\Person;
use App\Entity\Wallet;
use App\Entity\Product;

class PersonTest extends TestCase
{
    protected Person $person1;
    protected Person $person2;
    protected array $persons;
    protected Product $product;

    public function setUp(): void
    {
        $this->person1 = new Person('John', 'EUR');
        $this->person1->getWallet()->setBalance(200);
        $this->person2 = new Person('Michael', 'EUR');
        $this->person2->getWallet()->setBalance(0);
        $this->persons = [];
        $this->product = new Product('Twitter Blue', ['EUR' => 8.99], 'tech');
    }

    public function testPerson()
    {
        $this->assertEquals('John', $this->person1->getName());
        $this->assertEquals('EUR', $this->person1->getWallet()->getCurrency());
    }

    public function testSetName()
    {
        $this->person1->setName('Jane');
        $this->assertEquals('Jane', $this->person1->getName());
    }

    public function testSetWallet()
    {
        $this->person1->setWallet(new Wallet('USD'));
        $this->assertEquals('USD', $this->person1->getWallet()->getCurrency());
    }

    public function testHasFund()
    {
        $this->assertTrue($this->person1->hasFund());
        $this->person1->getWallet()->setBalance(0);
        $this->assertEquals(false, $this->person1->hasFund());
    }

    public function testTransfertFundOK()
    {
        $this->person1->transfertFund(100,$this->person2);
        $this->assertEquals(100, $this->person2->getWallet()->getBalance());
        $this->assertEquals(100, $this->person1->getWallet()->getBalance());
    }

    public function testTransfertFundBadCurrency()
    {
        $this->person1->getWallet()->setCurrency('USD');
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Can\'t give money with different currencies');
        $this->person1->transfertFund(100,$this->person2);
    }

    public function testTransfertFundNegativeAmount()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid amount');
        $this->person1->transfertFund(-100,$this->person2);
    }

    public function testTransfertFundInsufficientFund()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient fund');
        $this->person1->transfertFund(300,$this->person2);
    }

    public function testDivideWallet()
    {
        $this->persons[] = $this->person1;        
        $this->persons[] = $this->person2;
        $this->person1->divideWallet($this->persons);
        $this->assertEquals(100, $this->person2->getWallet()->getBalance());
        $this->assertEquals(100, $this->person1->getWallet()->getBalance());
    }

    public function testDivideWalletDifferentCurrency()
    {
        $this->person1->getWallet()->setCurrency('USD');
        $this->persons[] = $this->person1;        
        $this->persons[] = $this->person2;
        $this->person1->divideWallet($this->persons);
        $this->assertEquals(200, $this->person1->getWallet()->getBalance());
        $this->assertEquals(0, $this->person2->getWallet()->getBalance());
    }

    public function testBuyProductOK()
    {
        $this->person1->buyProduct($this->product);
        $this->assertEquals(191.01, $this->person1->getWallet()->getBalance());
    }

    public function testBuyProductInsufficientFund()
    {
        $this->person1->getWallet()->setBalance(0);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient fund');
        $this->person1->buyProduct($this->product);
    }

    public function testBuyProductDifferentCurrency()
    {
        $this->person1->getWallet()->setCurrency('USD');
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Can\'t buy product with this wallet currency');
        $this->person1->buyProduct($this->product);
    }
}