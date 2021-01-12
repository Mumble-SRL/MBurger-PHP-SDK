<?php

namespace Mumble\MBurger\Tests;

use Mumble\MBurger\MBurger;
use Mumble\MBurger\Exceptions\MBurgerUnauthorizedException;
use Mumble\MBurger\Exceptions\MBurgerNotFoundException;
use Mumble\MBurger\Exceptions\MBurgerInvalidRequestException;
use Illuminate\Support\Arr;

class BlockTest extends TestCase
{
    public function test_it_can_get_blocks()
    {
        $response = (new MBurger())->getBlocks();

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertArrayHasKey('meta', $response['body']);
        $this->assertArrayHasKey('items', $response['body']);
    }

    public function test_it_can_get_blocks_with_skip_take()
    {
        $response = (new MBurger())->skip(1)->take(1)->getBlocks();

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertCount(1, $response['body']['items']);
        $this->assertEquals(115, $response['body']['items'][0]['id']);
    }

    public function test_it_can_get_blocks_with_inverse_sort()
    {
        $response = (new MBurger())->sortBy('id', 'desc')->getBlocks();

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertArrayHasKey('items', $response['body']);
        $this->assertEquals([1569, 966, 117, 115, 114], Arr::pluck($response['body']['items'], 'id'));
    }

    public function test_it_can_get_blocks_with_sort_by_title()
    {
        $response = (new MBurger())->sortBy('title')->getBlocks();

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertArrayHasKey('items', $response['body']);
        $this->assertEquals([1569, 117, 114, 966, 115], Arr::pluck($response['body']['items'], 'id'));
    }

    public function test_it_can_get_blocks_with_structure()
    {
        $response = (new MBurger())->includeStructure()->getBlocks();

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertArrayHasKey('structure', $response['body']['items'][0]);
    }

    public function test_it_can_get_blocks_with_sections()
    {
        $response = (new MBurger())->includeSections()->getBlocks();

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertArrayHasKey('sections', $response['body']['items'][0]);
    }

    public function test_it_can_get_blocks_with_sections_and_elements()
    {
        $response = (new MBurger())
            ->includeSections()
            ->includeElements()
            ->getBlocks();

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertArrayHasKey('sections', $response['body']['items'][0]);
        $this->assertArrayHasKey('elements', $response['body']['items'][0]['sections'][0]);
    }

    public function test_it_can_get_blocks_filtered_by_id()
    {
        $response = (new MBurger())
            ->filterByIds([114])
            ->getBlocks();

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertEquals(114, $response['body']['items'][0]['id']);
    }

    public function test_it_can_get_blocks_filtered_by_multiple_id()
    {
        $response = (new MBurger())
            ->filterByIds([114, 113, 117])
            ->getBlocks();

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertCount(2, $response['body']['items']);
        $this->assertEquals(114, $response['body']['items'][0]['id']);
        $this->assertEquals(117, $response['body']['items'][1]['id']);
    }

    public function test_it_can_get_blocks_filtered_by_title()
    {
        $response = (new MBurger())
            ->filterByTitle('News')
            ->getBlocks();

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertCount(1, $response['body']['items']);
        $this->assertEquals(115, $response['body']['items'][0]['id']);
    }

    public function test_it_throws_exception_with_value_filter()
    {
        try {
            (new MBurger())->filterByValue(['value'])->getBlocks();
        } catch (MBurgerInvalidRequestException $e) {
            $this->assertEquals('Requested filter(s) `value` are not allowed. Allowed filter(s) are `id, title`.', $e->getMessage());
        }
    }

    public function test_it_can_get_block()
    {
        $response = (new MBurger())->getBlock(114);

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertEquals('Home', $response['body']['title']);
    }

    public function test_it_fails_with_invalid_id()
    {
        try {
            (new MBurger())->getBlock(999);
        } catch (MBurgerNotFoundException $e) {
            $this->assertEquals('The requested resource can not be found.', $e->getMessage());
        }
    }

    public function test_it_fails_with_unauthorized_user()
    {
        try {
            (new MBurger())->getBlock(10);
        } catch (MBurgerUnauthorizedException $e) {
            $this->assertEquals('This action is unauthorized.', $e->getMessage());
        }
    }

    public function test_it_can_get_block_with_structure()
    {
        $response = (new MBurger())->includeStructure()->getBlock(114);

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertArrayHasKey('structure', $response['body']);
    }

    public function test_it_can_get_block_with_sections()
    {
        $response = (new MBurger())->includeSections()->getBlock(114);

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertArrayHasKey('sections', $response['body']);
    }

    public function test_it_can_get_block_with_sections_and_structure()
    {
        $response = (new MBurger())
            ->includeSections()
            ->includeStructure()
            ->getBlock(114);

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertArrayHasKey('sections', $response['body']);
        $this->assertArrayHasKey('structure', $response['body']);
    }

    public function test_it_can_get_block_with_sections_and_elements()
    {
        $response = (new MBurger())
            ->includeSections()
            ->includeElements()
            ->getBlock(114);

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertArrayHasKey('sections', $response['body']);
        $this->assertArrayHasKey('elements', $response['body']['sections'][0]);
    }
}
