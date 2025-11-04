<?php

declare(strict_types=1);

namespace Lightit\Clinics\App\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Lightit\Clinics\Domain\DataTransferObjects\ClinicDto;
use Lightit\Doctors\Domain\Rules\ValidDoctorIds;

class UpsertClinicRequest extends FormRequest
{
    public const string NAME = 'name';

    public const string ADDRESS = 'address';

    public const string DOCTOR_IDS = 'doctor_ids';

    /**
     * @return array<string, list<string|ValidationRule>>
     */
    public function rules(): array
    {
        return [
            self::NAME => ['required', 'string', 'min:3', 'max:100'],
            self::ADDRESS => ['required', 'string', 'min:3', 'max:100'],
            self::DOCTOR_IDS => ['sometimes', 'array', new ValidDoctorIds()],
        ];
    }

    public function toDto(): ClinicDto
    {
        /** @var array<int> $doctorIds */
        $doctorIds = $this->array(self::DOCTOR_IDS);

        return new ClinicDto(
            name: $this->string(self::NAME)->toString(),
            address: $this->string(self::ADDRESS)->toString(),
            doctorIds: $doctorIds
        );
    }
}
