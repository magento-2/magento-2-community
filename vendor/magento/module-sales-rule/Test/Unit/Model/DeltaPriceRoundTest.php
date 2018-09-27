<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SalesRule\Test\Unit\Model;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\SalesRule\Model\DeltaPriceRound;

class DeltaPriceRoundTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PriceCurrencyInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $priceCurrency;

    /**
     * @var DeltaPriceRound
     */
    private $model;

    protected function setUp()
    {
        $this->priceCurrency = $this->getMockForAbstractClass(PriceCurrencyInterface::class);
        $this->priceCurrency->method('round')
            ->willReturnCallback(
                function ($amount) {
                    return round($amount, 2);
                }
            );

        $this->model = new DeltaPriceRound($this->priceCurrency);
    }

    /**
     * Tests rounded price based on previous rounding operation delta.
     *
     * @param array $prices
     * @param array $roundedPrices
     * @dataProvider roundDataProvider
     */
    public function testRound(array $prices, array $roundedPrices)
    {
        foreach ($prices as $key => $price) {
            $roundedPrice = $this->model->round($price, 'test');
            $this->assertEquals($roundedPrices[$key], $roundedPrice);
        }

        $this->model->reset('test');
    }

    /**
     * @return array
     */
    public function roundDataProvider()
    {
        return [
            [
                'prices' => [1.004, 1.004],
                'rounded prices' => [1.00, 1.01],
            ],
            [
                'prices' => [1.005, 1.005],
                'rounded prices' => [1.01, 1.0],
            ]
        ];
    }

    public function testReset()
    {
        $this->assertEquals(1.44, $this->model->round(1.444, 'test'));
        $this->model->reset('test');
        $this->assertEquals(1.44, $this->model->round(1.444, 'test'));
    }

    public function testResetAll()
    {
        $this->assertEquals(1.44, $this->model->round(1.444, 'test1'));
        $this->assertEquals(1.44, $this->model->round(1.444, 'test2'));

        $this->model->resetAll();

        $this->assertEquals(1.44, $this->model->round(1.444, 'test1'));
        $this->assertEquals(1.44, $this->model->round(1.444, 'test2'));
    }
}
