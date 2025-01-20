<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Entity\Wallet;

class WalletTest extends TestCase
{
    protected Wallet $wallet;

    protected function setUp(): void
    {
        $this->wallet = new Wallet('EUR');
    }

    public function testInitialBalance()
    {
        $this->assertEquals(0, $this->wallet->getBalance());
    }

    public function testSetBalanceOK()
    {
        $this->wallet->setBalance(100);
        $this->assertEquals(100, $this->wallet->getBalance());
    }

    public function testSetBalanceNegativeValue()
    {
        $this->expectException(\Exception::class);
        $this->wallet->setBalance(-10);
    }

    public function testSetCurrencyOK()
    {
        $this->wallet->setCurrency('USD');
        $this->assertEquals('USD', $this->wallet->getCurrency());
    }

    public function testSetCurrencyInvalidCurrency()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid currency');
        $this->wallet->setCurrency('GBP');
    }

    public function testAddFundOK()
    {
        $this->wallet->addFund(50);
        $this->assertEquals(50, $this->wallet->getBalance());
    }

    public function testAddFundNegativeValue()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid amount');
        $this->wallet->addFund(-20);
    }

    public function testRemoveFundOK()
    {
        $this->wallet->setBalance(100);
        $this->wallet->removeFund(50);
        $this->assertEquals(50, $this->wallet->getBalance());
    }

    public function testRemoveFundNegativeValue()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid amount');
        $this->wallet->removeFund(-20);
    }

    public function testRemoveFundInsufficientFunds()
    {
        $this->wallet->setBalance(30);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient funds');
        $this->wallet->removeFund(50);
    }
}