<?php /** @noinspection SpellCheckingInspection */

namespace Tests\Unit;

use Illuminate\Support\Carbon as Date;
use Tests\TestCase;
use App\Services\ValidateVacationService;
use App\DTO\VacationRequestDTO;

class ValidateVacationTest extends TestCase
{

    protected $connection = 'mysql_testing';


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
        $startDate = Date::create(2023, 10, 16);
        $endDate = $startDate->copy();

        $vacationRequest = new VacationRequestDTO(
            1,
            $startDate,
            $endDate,
            'payé',
            'Ne se prononce pas'
        );
        $this->assertTrue($this->validateVacationService->validateVacation(
            $vacationRequest,
            1,
            60,
            0,
            10
        ));
    }

    // solde de congés insufisant mais type de congé non payé et disponibilité
    // de l'equipe >= 50% pour la période demandée
    public function test2(): void
    {
        $startDate = Date::create(2023, 10, 16);
        $endDate = $startDate->copy();
        $endDate->addDays(2);
        $vacationRequest = new VacationRequestDTO(
            1,
            $startDate,
            $endDate,
            'non payé',
            'Ne se prononce pas'
        );
        $this->assertTrue($this->validateVacationService->validateVacation(
            $vacationRequest,
            0,
            50,
            0,
            4
        ));
    }

    // solde de congés suffisant pour la demande de congés payé mais disponibilité de l'equipe
    // < 50% pour la période demandée mais > 20% et ancienneté > 1 an

    public function test3(): void
    {
        $startDate = Date::create(2023, 10, 16);
        $endDate = $startDate->copy();
        $endDate->addDays(2);
        $vacationRequest = new VacationRequestDTO(
            1,
            $startDate,
            $endDate,
            'payé',
            'Ne se prononce pas'
        );
        $this->assertTrue($this->validateVacationService->validateVacation(
            $vacationRequest,
            3,
            30,
            1,
            10
        ));
    }

//Solde de congés suffisant pour la demande de congés, disponibilité de l'équipe
// < 20% pour la période demandée, mais motif de la demande est "important.",

    public function test4(): void
    {
        $startDate = Date::create(2023, 10, 16);
        $endDate = $startDate->copy();
        $endDate->addDays(2);
        $vacationRequest = new VacationRequestDTO(
            1,
            $startDate,
            $endDate,
            'payé',
            'Maladie grave'
        );
        $this->assertTrue($this->validateVacationService->validateVacation(
            $vacationRequest,
            4,
            10,
            0,
            10
        ));
    }
    //Solde de congés insuffisant pour la demande de congés, disponibilité de l'équipe
// < 20% pour la période demandée, mais motif de la demande est "important.",

    public function test5(): void
    {
        $startDate = Date::create(2023, 10, 16);
        $endDate = $startDate->copy();
        $endDate->addDays(2);
        $vacationRequest = new VacationRequestDTO(
            1,
            $startDate,
            $endDate,
            'payé',
            'Décès dans la famille'
        );
        $this->assertTrue($this->validateVacationService->validateVacation(
            $vacationRequest,
            1,
            10,
            0,
            10
        ));
    }
    // solde de congés suffisant pour la demande de congés mais disponibilité de l'equipe < 20% pour
    // la période demandée et ancienneté > 1 an mais motif de la demande est "important"
    public function test6(): void
    {
        $startDate = Date::create(2023, 10, 16);
        $endDate = $startDate->copy();
        $endDate->addDays(2);
        $vacationRequest = new VacationRequestDTO(
            1,
            $startDate,
            $endDate,
            'payé',
            'Décès dans la famille'
        );
        $this->assertTrue($this->validateVacationService->validateVacation(
            $vacationRequest,
            4,
            10,
            1,
            1
        ));
    }
    /**
     * Tests négatifs.
     */
    // solde de congés insuffisant pour la demande de congés payés
    public function test7(): void
    {
        $startDate = Date::create(2023, 10, 16);
        $endDate = $startDate->copy();
        $endDate->addDays(2);
        $vacationRequest = new VacationRequestDTO(
            1,
            $startDate,
            $endDate,
            'payé',
            'Ne se prononce pas'
        );
        $this->assertFalse($this->validateVacationService->validateVacation(
            $vacationRequest,
            2,
            10,
            1,
            1
        ));
    }

    // Demande de congé pour une période trop éloignée dans le futur > 1 an
    public function test8(): void
    {
        $startDate = Date::now()->addYears(2);
        $endDate = $startDate->copy();
        $endDate->addDays(2);
        $vacationRequest = new VacationRequestDTO(
            1,
            $startDate,
            $endDate,
            'payé',
            'Décès dans la famille'
        );
        $this->assertFalse($this->validateVacationService->validateVacation(
            $vacationRequest,
            4,
            90,
            1,
            1
        ));
    }

    // solde de congés suffisant pour la demande de congés mais disponibilité
    // de l'equipe < 50% pour la période demandée et ancienneté < 1 an
    public function test9(): void
    {
        $startDate = Date::create(2023, 10, 16);
        $endDate = $startDate->copy();
        $endDate->addDays(2);
        $vacationRequest = new VacationRequestDTO(
            1,
            $startDate,
            $endDate,
            'payé',
            'Ne se prononce pas'
        );
        $this->assertFalse($this->validateVacationService->validateVacation(
            $vacationRequest,
            4,
            40,
            0,
            1
        ));
    }

    // solde de congés suffisant pour la demande de congés mais disponibilité de l'equipe < 20% pour la
    // période demandée et ancienneté > 1 an mais motif de la demande est "pas important."
    public function test10(): void
    {
        $startDate = Date::create(2023, 10, 16);
        $endDate = $startDate->copy();
        $endDate->addDays(2);
        $vacationRequest = new VacationRequestDTO(
            1,
            $startDate,
            $endDate,
            'payé',
            'Ne se prononce pas'
        );
        $this->assertFalse($this->validateVacationService->validateVacation(
            $vacationRequest,
            4,
            10,
            2,
            1
        ));
    }

    // solde de congés insufisant pour la demande de congés impayé mais disponibilité de l'equipe < 50% pour
    // la période demandée peut importe lancièneté

    public function test11(): void
    {
        $startDate = Date::create(2023, 10, 16);
        $endDate = $startDate->copy();
        $endDate->addDays(2);
        $vacationRequest = new VacationRequestDTO(
            1,
            $startDate,
            $endDate,
            'non payé',
            'Ne se prononce pas'
        );
        $this->assertFalse($this->validateVacationService->validateVacation(
            $vacationRequest,
            0,
            40,
            3,
            1
        ));
    }

    // si il a demander plus de 4 congé non payé dans l'année en cours et que ce n'est pas un motif important
    public function test12(): void
    {
        $startDate = Date::create(2023, 10, 16);
        $endDate = $startDate->copy();
        $endDate->addDays(2);
        $vacationRequest = new VacationRequestDTO(
            1,
            $startDate,
            $endDate,
            'non payé',
            'Ne se prononce pas'
        );
        $this->assertFalse($this->validateVacationService->validateVacation(
            $vacationRequest,
            4,
            60,
            0,
            5
        ));
    }

    // cas positif

    // cas ou il a pas assez de solde de congé par apport au nombre
    // de jour demander mais lorsque quon déduit les jour férié et week end il a
    // assez de solde de congé
    // on enlève 2 jours du weekend du 23 et 24 décembre et 1 jours férié
    // le 25 décembre donc 2 jours de congé suffisent
    public function test13(): void
    {
        $startDate = Date::create(2023, 12, 22);
        $endDate = $startDate->copy();
        $endDate->addDays(4);
        $vacationRequest = new VacationRequestDTO(
            1,
            $startDate,
            $endDate,
            'non payé',
            'Ne se prononce pas'
        );
        $this->assertTrue($this->validateVacationService->validateVacation(
            $vacationRequest,
            2,
            70,
            0,
            2
        ));
    }
}
