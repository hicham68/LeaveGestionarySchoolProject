<?php

namespace App\DTO;

use Illuminate\Support\Carbon as Date;

class VacationRequestDTO
{
    public int $employee_id;
    public Date $start_date;
    public Date $end_date;
    public string $vacation_type;
    public string $reason;

    public function __construct(
        int    $employee_id,
        Date $start_date,
        Date $end_date,
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
