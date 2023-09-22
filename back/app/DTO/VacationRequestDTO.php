<?php

namespace App\DTO;

class VacationRequestDTO
{
    public int $employee_id;
    public string $start_date;
    public string $end_date;
    public string $vacation_type;
    public string $reason;

    public function __construct(
        int    $employee_id,
        string $start_date,
        string $end_date,
        string $vacation_type,
        string $reason
    ) {
        $this->employee_id = $employee_id;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->vacation_type = $vacation_type;
        $this->reason = $reason;
    }
}
