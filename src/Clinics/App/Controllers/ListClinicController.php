<?php

declare(strict_types=1);

namespace Lightit\Clinics\App\Controllers;

use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Lightit\Clinics\App\Resources\ClinicResource;
use Lightit\Clinics\Domain\Actions\ListClinicAction;

#[Group('Clinics')]
final readonly class ListClinicController
{
    public function __invoke(
        ListClinicAction $action,
    ): JsonResponse {
        $clinics = $action->execute();

        return ClinicResource::collection($clinics)
            ->response();
    }
}
