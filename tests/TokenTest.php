<?php

namespace Mumble\MBurger\Tests;

use Mumble\MBurger\MBurger;
use Mumble\MBurger\Exceptions\MBurgerUnauthenticatedException;

class TokenTest extends TestCase
{
    public function test_wrong_missing_token()
    {
        $this->app['config']->set('mburger.api_key', null);

        try {
            (new MBurger())->getProject();
        } catch (MBurgerUnauthenticatedException $e) {
            $this->assertEquals('The project token is not present.', $e->getMessage());
        }
    }

    public function test_wrong_project_token()
    {
        $this->app['config']->set('mburger.api_key', 'WRONG');

        try {
            (new MBurger())->getProject();
        } catch (MBurgerUnauthenticatedException $e) {
            $this->assertEquals('The project token is not valid.', $e->getMessage());
        }
    }

    public function test_project_token_in_constructor()
    {
        $response = (new MBurger($this->app['config']->get('mburger.api_key')))->getProject();

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertEquals('Mumble', $response['body']['name']);
    }
}
