<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EventsControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    public function testGetAllEventsWithValidDates()
    {
        $startDate = '2022-01-10';
        $endDate = '2022-01-20';

        $response = $this->postJson("/api/events?start_date={$startDate}&end_date={$endDate}");
        $response->assertStatus(200)
                 ->assertJsonStructure(['success', 'data']);
    }

    public function testGetAllEventsWithoutValidDates()
    {
        $endDate = '';

        $response = $this->postJson("/api/events?end_date={$endDate}");
        $response->assertStatus(200)
                 ->assertJsonStructure(['success', 'message']);
    }

    public function testGetNextWeekFlights()
    {
        $response = $this->getJson("/api/nextWeekFlight");
        $response->assertStatus(200)
                 ->assertJsonStructure(['success', 'data']);
    }

    public function testGetNextWeekStandBy()
    {
        $response = $this->getJson("/api/nextWeekStandBy");
        $response->assertStatus(200)
                 ->assertJsonStructure(['success', 'data']);
    }

    public function testGetFlightsFromStartLocationWithValidLocation()
    {
        $location = 'KRP';

        $response = $this->postJson("/api/getFlights?location={$location}");
        $response->assertStatus(200)
                 ->assertJsonStructure(['success', 'data']);
    }

    public function testGetFlightsFromStartLocationWithInValidLocation()
    {
        $location = 'KRPX';

        $response = $this->postJson("/api/getFlights?location={$location}");
        $response->assertStatus(200)
                 ->assertJsonStructure(['success', 'data']);
    }

    public function testGetFlightsFromStartLocationWithoutValidLocation()
    {
        $response = $this->postJson("/api/getFlights");
        $response->assertStatus(200)
                 ->assertJsonStructure(['success', 'message']);
    }
}
