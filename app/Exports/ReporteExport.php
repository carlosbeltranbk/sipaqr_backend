<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

Use App\DiaFeriado;


class ReporteExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return DiaFeriado::all();
    }
}
