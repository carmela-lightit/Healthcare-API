<?php

declare(strict_types=1);

namespace Lightit\Appointments\Domain\Actions;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Lightit\Appointments\Domain\Models\Appointment;
use Spatie\QueryBuilder\QueryBuilder;

class ListMyAppointmentsAction
{
    /**
     * @return LengthAwarePaginator<int, Appointment>
     */
    public function execute(): LengthAwarePaginator
    {
        return QueryBuilder::for(Appointment::class)
            ->allowedFilters(['doctor', 'clinic'])
            ->defaultSort('starts_at')
            ->allowedSorts(['starts_at', 'doctor', 'clinic'])
            ->where('user_id', Auth::id())
            ->with(['doctor', 'clinic', 'user'])
            ->paginate();
    }
}
