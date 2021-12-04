<?php

namespace FintechSystems\Payfast\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Dotenv\Dotenv;

class ApiTest extends TestCase
{
    /** @test */
    public function it_can_read_the_test_merchant_id_from_the_env_testing_file()
    {
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__.'/../.env.testing');

        $client = [
            'merchant_id' => $_ENV['PAYFAST_MERCHANT_ID'],
        ];

        $this->assertEquals(10004002, $client['merchant_id']);
    }
}
