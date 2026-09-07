<?php

use App\Services\RazorpayService;
use Razorpay\Api\Api;
use Razorpay\Api\Collection;
use Razorpay\Api\Entity;
use Razorpay\Api\Order;
use Tests\TestCase;

uses(TestCase::class);

test('fetchOrderPayments gracefully handles SDK bug where items is an empty Collection instance', function () {
    $ref = new ReflectionMethod(Entity::class, 'buildEntity');
    $ref->setAccessible(true);

    // This simulates the exact object returned by Razorpay PHP SDK for 0 payments:
    // $order->payments() returns a Collection whose ->items is ALSO a Collection object, not an array.
    $emptyPaymentsCollection = $ref->invoke(null, [
        'entity' => 'collection',
        'count' => 0,
        'items' => [],
    ]);

    expect($emptyPaymentsCollection->items)->toBeInstanceOf(Collection::class);

    $orderMock = Mockery::mock(Order::class);
    $orderMock->shouldReceive('payments')->once()->andReturn($emptyPaymentsCollection);

    $apiMock = Mockery::mock(Api::class);
    $apiMock->order = Mockery::mock();
    $apiMock->order->shouldReceive('fetch')->with('order_empty_test')->once()->andReturn($orderMock);

    $service = new RazorpayService($apiMock);
    $payments = $service->fetchOrderPayments('order_empty_test');

    expect($payments)->toBeArray()
        ->and($payments)->toBeEmpty();
});

test('fetchOrderPayments parses payments correctly when payments exist in collection', function () {
    $ref = new ReflectionMethod(Entity::class, 'buildEntity');
    $ref->setAccessible(true);

    $populatedCollection = $ref->invoke(null, [
        'entity' => 'collection',
        'count' => 1,
        'items' => [
            [
                'entity' => 'payment',
                'id' => 'pay_abc123',
                'amount' => 35000,
                'status' => 'captured',
            ],
        ],
    ]);

    $orderMock = Mockery::mock(Order::class);
    $orderMock->shouldReceive('payments')->once()->andReturn($populatedCollection);

    $apiMock = Mockery::mock(Api::class);
    $apiMock->order = Mockery::mock();
    $apiMock->order->shouldReceive('fetch')->with('order_populated_test')->once()->andReturn($orderMock);

    $service = new RazorpayService($apiMock);
    $payments = $service->fetchOrderPayments('order_populated_test');

    expect($payments)->toHaveCount(1)
        ->and($payments[0]['id'])->toBe('pay_abc123')
        ->and($payments[0]['status'])->toBe('captured')
        ->and($payments[0]['amount'])->toBe(35000);
});

test('fetchOrderPayments catches any Throwable or API errors and returns empty array', function () {
    $apiMock = Mockery::mock(Api::class);
    $apiMock->order = Mockery::mock();
    $apiMock->order->shouldReceive('fetch')->with('order_err_test')->andThrow(new Exception('Network timeout'));

    $service = new RazorpayService($apiMock);
    $payments = $service->fetchOrderPayments('order_err_test');

    expect($payments)->toBeArray()
        ->and($payments)->toBeEmpty();
});
