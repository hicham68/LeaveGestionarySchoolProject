<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class VacationRequestTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * Test the /api/users route.
     *
     * @return void
     */
    public function testCreateVacationRequest(): void
    {
        $response = $this->postJson('/api/demande-conge', [
            'employee_id' => 24,
            'vacation_type_id' => 1,
            'reason_id' => 1,
            'start_date' => Carbon::now()->addWeek()->startOfWeek()->addDay(),
            'end_date' => Carbon::now()->addWeek()->startOfWeek()->addDay()->addDays(),
        ]);
        $response->assertStatus(201);
    }

    public function testGetAllVacationRequest(): void
    {
        $response = $this->getJson('/api/demande-conge');

        $response->assertStatus(200);
    }

    public function testGetVacationRequest(): void
    {
        $response = $this->getJson('/api/demande-conge/7');

        $response->assertStatus(200);
    }

    public function testGetVacationRequestByEmployeeId(): void
    {
        $response = $this->getJson('/api/demande-conge/employee/24');

        $response->assertStatus(200);
    }

    public function testUpdateVacationRequest(): void
    {
        $response = $this->patchJson('/api/demande-conge/7', [
            'start_date' => Carbon::now()->addWeek()->startOfWeek()->addDay(),
            'end_date' => Carbon::now()->addWeek()->startOfWeek()->addDays(2),
        ]);
        $response->assertStatus(201);
    }

    public function testDeleteVacationRequest(): void
    {
        $response = $this->deleteJson('/api/demande-conge/7');

        $response->assertStatus(200);
    }

    public function testGetVacationBalance(): void
    {
        $response = $this->getJson('/api/solde-conge/employee/24');

        $response->assertStatus(200);
    }

    // test error 404
    public function testGetVacationRequestError(): void
    {
        $response = $this->getJson('/api/demande-conge/100');

        $response->assertStatus(404);
    }

    // test error 404
    public function testGetVacationRequestByEmployeeIdError(): void
    {
        $response = $this->getJson('/api/demande-conge/employee/100');
        $response->assertStatus(404);
    }

    // test error 404
    public function testUpdateVacationRequestError(): void
    {
        $response = $this->patchJson('/api/demande-conge/100', [
            'start_date' => Carbon::now()->addWeek()->startOfWeek()->addDay(),
            'end_date' => Carbon::now()->addWeek()->startOfWeek()->addDay()->addDays(),
        ]);

        $response->assertStatus(404);
    }

    // test error 404
    public function testDeleteVacationRequestError(): void
    {
        $response = $this->deleteJson('/api/demande-conge/100');

        $response->assertStatus(404);
    }

    // test error 404
    public function testGetVacationBalanceError(): void
    {
        $response = $this->getJson('/api/solde-conge/employee/100');

        $response->assertStatus(404);
    }
}
