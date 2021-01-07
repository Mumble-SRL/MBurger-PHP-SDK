<?php

namespace Mumble\MBurger\Tests;

use Mumble\MBurger\MBurger;

class ProjectTest extends TestCase
{
    public function test_it_can_get_project()
    {
        $response = (new MBurger())->getProject();

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertEquals('Mumble', $response['body']['name']);
    }

    public function test_it_can_get_project_with_blocks()
    {
        $response = (new MBurger())->includeBlocks()->getProject();

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertArrayHasKey('blocks', $response['body']);
        $this->assertEquals('Home', $response['body']['blocks'][0]['title']);
    }

    public function test_it_can_get_project_with_blocks_and_structure()
    {
        $response = (new MBurger())->includeBlocks()
            ->includeStructure()
            ->getProject();

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertArrayHasKey('structure', $response['body']['blocks'][0]);
        $this->assertEquals('title', $response['body']['blocks'][0]['structure'][0]['name']);
    }

    public function test_it_can_get_project_with_blocks_and_sections()
    {
        $response = (new MBurger())->includeBlocks()
            ->includeSections()
            ->getProject();

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertArrayHasKey('sections', $response['body']['blocks'][0]);
    }

    public function test_it_can_get_project_with_blocks_and_sections_and_elements()
    {
        $response = (new MBurger())->includeBlocks()
            ->includeSections()
            ->includeElements()
            ->getProject();

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertArrayHasKey('sections', $response['body']['blocks'][0]);
        $this->assertArrayHasKey('elements', $response['body']['blocks'][0]['sections'][0]);
    }

    public function test_it_can_get_project_with_contracts()
    {
        $response = (new MBurger())->includeContracts()->getProject();

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertArrayHasKey('contracts', $response['body']);
        $this->assertEquals('Test', $response['body']['contracts'][0]['name']);
    }

    public function test_it_can_get_project_with_beacons()
    {
        $response = (new MBurger())->includeBeacons()->getProject();

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertEquals('Mumble', $response['body']['name']);
        $this->assertArrayHasKey('beacons', $response['body']);
        $this->assertEquals('Test', $response['body']['beacons'][0]['title']);
    }
}
