<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

enum PatientAgeType
{
    case Years;
    case Months;
    case Days;
}

class Patient extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'first_name',
        'last_name',
        'birthdate',
    ];

    protected $casts = [
        'birthdate' => 'date',
    ];

    /**
     * Get's a cache entry for the patient.
     *
     * @var array<string, mixed>
     */
    public function getCacheEntryAttribute()
    {
        $todayDate = Carbon::now();
        $age = $this->birthdate->diffForHumans($todayDate, [
            'parts' => 1,
            'syntax' => CarbonInterface::DIFF_ABSOLUTE,
            'skip' => ['weeks'],
            'minimumUnit' => 'day',
        ]);

        return [
            'name' => $this->name,
            'birthdate' => $this->birthdate->format('d.m.Y'),
            'age' => $age,
        ];
    }

    /**
     * Get the patient's age.
     *
     * @return string
     */
    public function getAgeAttribute(): int
    {
        $todayDate = Carbon::now();

        return $todayDate->diffInYears($this->birthdate) ??
            $todayDate->diffInMonths($this->birthdate) ??
            $todayDate->diffInDays($this->birthdate);
    }

    /**
     * Get the patient's age type.
     *
     * @return string
     */
    public function getAgeTypeAttribute(): PatientAgeType
    {
        $todayDate = Carbon::now();
        if ($todayDate->diffInYears($this->birthdate)) {
            return PatientAgeType::Years;
        } elseif ($todayDate->diffInMonths($this->birthdate)) {
            return PatientAgeType::Months;
        }

        return PatientAgeType::Days;
    }

    /**
     * Get the patient's full name.
     */
    public function getNameAttribute(): string
    {
        return $this->first_name.' '.$this->last_name;
    }
}
