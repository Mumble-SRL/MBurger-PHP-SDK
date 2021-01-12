<?php

namespace Mumble\MBurger\Tests;

use Mumble\MBurger\MBurger;
use Mumble\MBurger\Exceptions\MBurgerUnauthorizedException;
use Mumble\MBurger\Exceptions\MBurgerNotFoundException;
use Mumble\MBurger\Exceptions\MBurgerInvalidRequestException;
use Mumble\MBurger\Exceptions\MBurgerValidationException;
use Illuminate\Support\Arr;

class SectionTest extends TestCase
{
    public function test_it_can_get_sections()
    {
        $response = (new MBurger())->getSections(114);

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertArrayHasKey('meta', $response['body']);
        $this->assertArrayHasKey('items', $response['body']);
    }

    public function test_it_can_get_sections_with_skip_take()
    {
        $response = (new MBurger())->skip(1)->take(2)->getSections(114);

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertCount(2, $response['body']['items']);
        $this->assertEquals(4447, $response['body']['items'][0]['id']);
    }

    public function test_it_fails_with_invalid_skip_and_take()
    {
        try {
            (new MBurger())->skip(-3)->take(-5)->getSections(114);
        } catch (MBurgerValidationException $e) {
            $this->assertEquals('The given data was invalid.', $e->getMessage());
        }
    }

    public function test_it_can_get_sections_with_inverse_sort_available_at()
    {
        $response = (new MBurger())->sortBy('available_at', 'desc')->take(3)->getSections(114);

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertArrayHasKey('items', $response['body']);
        $this->assertEquals([10099, 9347, 8653], Arr::pluck($response['body']['items'], 'id'));
    }

    public function test_it_can_get_sections_with_order_sort()
    {
        $response = (new MBurger())->sortBy('order')->take(3)->getSections(114);

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertArrayHasKey('items', $response['body']);
        $this->assertEquals([160, 4447, 8653], Arr::pluck($response['body']['items'], 'id'));
    }

    public function test_it_can_get_sections_with_elements()
    {
        $response = (new MBurger())->includeElements()->getSections(114);

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertArrayHasKey('elements', $response['body']['items'][0]);
    }

    public function test_it_can_get_sections_with_beacons()
    {
        $response = (new MBurger())->includeBeacons()->getSections(114);

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertArrayHasKey('beacons', $response['body']['items'][0]);
    }

    public function test_it_can_get_sections_with_elements_and_beacons()
    {
        $response = (new MBurger())->includeElements()->includeBeacons()->getSections(114);

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertArrayHasKey('elements', $response['body']['items'][0]);
        $this->assertArrayHasKey('beacons', $response['body']['items'][0]);
    }

    public function test_it_fails_with_invalid_include()
    {
        try {
            (new MBurger())->includeSections()->getSections(114);
        } catch (MBurgerInvalidRequestException $e) {
            $this->assertEquals('Requested include(s) `sections` are not allowed. Allowed include(s) are `elements, elementsCount, beacons, beaconsCount`.', $e->getMessage());
        }
    }

    public function test_it_can_get_sections_with_distance()
    {
        $response = (new MBurger())
            ->distance(42.231232, 12.325322)
            ->getSections(1569);

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertArrayHasKey('distance', $response['body']['items'][0]['elements']['address']['value']);
    }

    public function test_it_can_get_sections_filtered_by_id()
    {
        $response = (new MBurger())
            ->filterByIds([4447])
            ->getSections(114);

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertEquals(4447, $response['body']['items'][0]['id']);
    }

    public function test_it_can_get_sections_filtered_by_multiple_id()
    {
        $response = (new MBurger())
            ->filterByIds([653, 4447, 9347])
            ->getSections(114);

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertCount(2, $response['body']['items']);
        $this->assertEquals([4447, 9347], Arr::pluck($response['body']['items'], 'id'));
    }

    public function test_it_can_get_sections_filtered_by_relation()
    {
        $response = (new MBurger())
            ->filterByRelation(114, 4447)
            ->getSections(115);

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertCount(1, $response['body']['items']);
        $this->assertEquals(4221, $response['body']['items'][0]['id']);
    }

    public function test_it_can_get_sections_filtered_by_value()
    {
        $response = (new MBurger())
            ->filterByValue(['Nuova', 'Ciao'])
            ->getSections(115);

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertCount(2, $response['body']['items']);
        $this->assertEquals([161, 10305], Arr::pluck($response['body']['items'], 'id'));
    }

    public function test_it_can_get_sections_filtered_by_value_within_element()
    {
        $response = (new MBurger())
            ->filterByValue(['werf'], 'content')
            ->getSections(115);

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertCount(1, $response['body']['items']);
        $this->assertEquals([10305], Arr::pluck($response['body']['items'], 'id'));
    }

    public function test_it_can_get_sections_filtered_by_geofence()
    {
        $response = (new MBurger())
            ->locale('it')
            ->filterByGeofence(44.602941, 44.568996, 10.883009, 10.789488)
            ->getSections(1569);

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertCount(1, $response['body']['items']);
        $this->assertEquals([321287], Arr::pluck($response['body']['items'], 'id'));

        $response = (new MBurger())
            ->filterByGeofence(44.602941, 44.602896, 10.883009, 10.789488)
            ->getSections(1569);

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertCount(0, $response['body']['items']);
    }

    public function test_it_can_get_sections_with_locale()
    {
        $response = (new MBurger())
            ->locale('it')
            ->includeElements()
            ->getSections(1569);

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertEquals('Test', $response['body']['items'][0]['elements']['title']['value']);

        $response = (new MBurger())
            ->locale('en')
            ->includeElements()
            ->getSections(1569);

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertEquals('', $response['body']['items'][0]['elements']['title']['value']);
    }

    public function test_it_can_get_sections_with_locale_forcing_fallback()
    {
        $response = (new MBurger())
            ->locale('it')
            ->forceLocaleFallback()
            ->includeElements()
            ->getSections(1569);

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertEquals('Test', $response['body']['items'][0]['elements']['title']['value']);

        $response = (new MBurger())
            ->locale('en')
            ->forceLocaleFallback()
            ->includeElements()
            ->getSections(1569);

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertEquals('Test', $response['body']['items'][0]['elements']['title']['value']);
    }

    public function test_it_can_get_section()
    {
        $response = (new MBurger())->getSection(321287);

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertEquals('321287', $response['body']['id']);
    }

    public function test_it_fails_with_invalid_id()
    {
        try {
            (new MBurger())->getSection(999);
        } catch (MBurgerNotFoundException $e) {
            $this->assertEquals('The requested resource can not be found.', $e->getMessage());
        }
    }

    public function test_it_fails_with_unauthorized_user()
    {
        try {
            (new MBurger())->getSection(8563);
        } catch (MBurgerUnauthorizedException $e) {
            $this->assertEquals('This action is unauthorized.', $e->getMessage());
        }
    }

    public function test_it_can_get_section_by_slug()
    {
        $response = (new MBurger())->getSection('321287-en');

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertEquals('321287', $response['body']['id']);

        $response = (new MBurger())->locale('it')->getSection('321287-it');

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertEquals('321287', $response['body']['id']);
    }

    public function test_it_fails_with_invalid_slug()
    {
        try {
            (new MBurger())->getSection('321287-en_WRONG');
        } catch (MBurgerNotFoundException $e) {
            $this->assertEquals('The requested resource can not be found.', $e->getMessage());
        }
    }

    public function test_it_can_get_section_by_slug_forcing_it()
    {
        $response = (new MBurger())->locale('it')->forceSlug()->getSection(123232312412331);

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertEquals('321288', $response['body']['id']);
    }

    public function test_it_can_get_section_with_elements()
    {
        $response = (new MBurger())->includeElements()->getSection(8653);

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertArrayHasKey('elements', $response['body']);
    }

    public function test_it_can_get_section_with_beacons()
    {
        $response = (new MBurger())->includeBeacons()->getSection(4447);

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['status_code']);
        $this->assertArrayHasKey('beacons', $response['body']);
    }
}
