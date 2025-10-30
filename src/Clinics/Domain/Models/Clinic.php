<?php

declare(strict_types=1);

namespace Lightit\Clinics\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Lightit\Doctors\Domain\Models\Doctor;

class Clinic extends Model
{
    protected $guarded = ['id'];

    /**
     * Doctors that work at this clinic.
     *
     * @return BelongsToMany<Doctor, Clinic>
     */
    public function doctors(): BelongsToMany
    {
        /** @var BelongsToMany<Doctor, Clinic> $relation */
        $relation = $this->belongsToMany(Doctor::class)->withTimestamps();

        return $relation;
    }
}
