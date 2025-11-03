<?php

declare(strict_types=1);

namespace Lightit\Doctors\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Lightit\Clinics\Domain\Models\Clinic;

class Doctor extends Model
{
    protected $guarded = ['id'];

    /**
     * @return BelongsToMany<Clinic, Doctor>
     */
    public function clinics(): BelongsToMany
    {
        /** @var BelongsToMany<Clinic, Doctor> $relation */
        $relation = $this->belongsToMany(Clinic::class);

        return $relation;
    }
}
