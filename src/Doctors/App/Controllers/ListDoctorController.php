<?php

declare(strict_types=1);

namespace Lightit\Doctors\App\Controllers;

use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Lightit\Doctors\App\Resources\DoctorResource;
use Lightit\Doctors\Domain\Actions\ListDoctorAction;

#[Group('Doctors')]
final readonly class ListDoctorController
{
    public function __invoke(
        ListDoctorAction $action,
    ): JsonResponse {
        $doctors = $action->execute();

        return DoctorResource::collection($doctors)
            ->response();
    }
}
