<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\ValidateVacationService;
use App\DTO\VacationRequestDTO;

class ValidateVacationTest extends TestCase
{

    private ValidateVacationService $validateVacationService;

    public function setUp(): void
    {
        parent::setUp();

        // Instanciez votre service ici
        $this->validateVacationService = app(ValidateVacationService::class);
    }

    /**
     * Tests positifs.
     */
    // solde de congés suffisant pour la demande de congés et disponibilité de l'equipe > 50% pour la période demandée
    public function test1(): void
    {
        $vacationRequest = new VacationRequestDTO(
            1,
            '2023-10-05',
            '2021-10-05',
            'payé',
            'Ne se prononce pas'
        );
        $this->assertTrue($this->validateVacationService->validateVacation(
            $vacationRequest,
            1,
            60,
            0,
            'payé',
            'Ne se prononce pas'
        ));
    }

    // solde de congés insufisant mais type de congé non payé et disponibilité
    // de l'equipe > 50% pour la période demandée
    public function test2(): void
    {
        $this->assertTrue();
    }

    // solde de congés suffisant pour la demande de congés mais disponibilité de l'equipe
    // < 50% pour la période demandée mais > 20% et ancienneté > 1 an

    public function test3(): void
    {
        $this->assertTrue();
    }

//Solde de congés suffisant pour la demande de congés, disponibilité de l'équipe
// < 50% pour la période demandée, mais motif de la demande est "important.",

    public function test4(): void
    {
        $this->assertTrue();
    }
    // solde de congés suffisant pour la demande de congés mais disponibilité de l'equipe < 50% pour
    // la période demandée et ancienneté > 1 an mais motif de la demande est "important"
    public function test5(): void
    {
        $this->assertTrue();
    }
    /**
     * Tests négatifs.
     */
    // solde de congés insuffisant pour la demande de congés payés
    public function test6(): void
    {
        $this->assertTrue();
    }

    // Demande de congé pour une période trop éloignée dans le futur > 1 an
    public function test7(): void
    {
        // Créez un employé avec un solde de congés suffisant


        // Configurez la demande de congé pour une période très éloignée dans le futur
        // Configurez la disponibilité de l'équipe à plus de 50%
        // Appelez la méthode de validation de la demande de congé
        // Vérifiez que la demande est refusée
        $this->assertFalse();
    }

    // solde de congés suffisant pour la demande de congés mais disponibilité
    // de l'equipe < 50% pour la période demandée et ancienneté < 1 an
    public function test8(): void
    {
        $this->assertFalse();
    }

    // solde de congés suffisant pour la demande de congés mais disponibilité de l'equipe < 50% pour la
    // période demandée et ancienneté > 1 an mais motif de la demande est "pas important."
    public function test9(): void
    {
        $this->assertFalse();
    }

    // solde de congés insufisant pour la demande de congés impayé mais disponibilité de l'equipe < 50% pour
    // la période demandée et ancienneté peut importe motif de la demande est "important mais demande pour dans plus 2 semaine."

    public function test10(): void
    {
        $this->assertFalse();
    }
}
