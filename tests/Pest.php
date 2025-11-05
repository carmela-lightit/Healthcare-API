<?php

declare(strict_types=1);

use Database\Factories\AppointmentFactory;
use Database\Factories\ClinicFactory;
use Database\Factories\DoctorFactory;
use Illuminate\Support\Str;
use Lightit\Appointments\Domain\Models\Appointment;
use Lightit\Clinics\Domain\Models\Clinic;
use Lightit\Doctors\Domain\Models\Doctor;
use Lightit\Users\Domain\Models\User;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(
    Tests\TestCase::class,
    Illuminate\Foundation\Testing\RefreshDatabase::class,
)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/


/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function getLongName(): string
{
    return Str::repeat(string: 'name', times: random_int(min: 30, max: 50));
}

/**
 * @return array{0: Doctor, 1: Clinic}
 */
function makeDoctorAndClinic(): array
{
    $doctor = DoctorFactory::new()->createOne();
    $clinic = ClinicFactory::new()->createOne();
    $doctor->clinics()->attach($clinic->id);

    return [$doctor, $clinic];
}

/**
 * @return array<Appointment>
 */
function makeAppointment(User $user, Doctor|null $doctor = null, Clinic|null $clinic = null): array
{
    if (! $doctor || ! $clinic) {
        [$doctor, $clinic] = makeDoctorAndClinic();
    }
    $appointment = AppointmentFactory::new()->createOne([
        'user_id' => $user->id,
        'doctor_id' => $doctor->id,
        'clinic_id' => $clinic->id,
    ]);

    return [$appointment];
}
