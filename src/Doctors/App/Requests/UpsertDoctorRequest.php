<?php

declare(strict_types=1);

namespace Lightit\Doctors\App\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Lightit\Clinics\Domain\Rules\ValidClinicIds;
use Lightit\Doctors\Domain\DataTransferObjects\DoctorDto;

class UpsertDoctorRequest extends FormRequest
{
    public const string NAME = 'name';

    public const string CLINIC_IDS = 'clinic_ids';

    /**
     * @return array<string, list<string|ValidationRule>>
     */
    public function rules(): array
    {
        return [
            self::NAME => ['required', 'string', 'min:3', 'max:100'],
            self::CLINIC_IDS => ['sometimes', 'array', new ValidClinicIds()],
        ];
    }

    public function toDto(): DoctorDto
    {
        /** @var array<int> $clinicIds */
        $clinicIds = $this->array(self::CLINIC_IDS);

        return new DoctorDto(
            name: $this->string(self::NAME)->toString(),
            clinicIds: $clinicIds
        );
    }
}
