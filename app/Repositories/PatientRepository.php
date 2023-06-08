<?php

namespace App\Repositories;

use App\Jobs\ProcessPatient;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class PatientRepository
{
    /**
     * Cache key for the patient list.
     */
    protected string $allPatientsCacheKey = 'patients.all';

    public function __construct()
    {
        /**
         * Time to live for the patient list cache.
         */
        $this->allPatientsCacheTtl = \config('PATIENTS_CACHE_TTL');
    }

    /**
     * Updates the patient list cache.
     *
     * @return array Array of patients info from the cache.
     */
    public function updateCache(): array
    {
        $cb = function () {
            $result = [];
            foreach ($this->getAll() as $patient) {
                $result[] = $patient->cacheEntry;
            }

            return $result;
        };
        Cache::forget($this->allPatientsCacheKey);

        return Cache::remember($this->allPatientsCacheKey, $this->allPatientsCacheTtl, $cb);
    }

    /**
     * Get's all patients from the database.
     *
     * @return Collection Collection of patients.
     */
    public function getAll(): Collection
    {
        return Patient::all();
    }

    /**
     * Get's all patient info from the cache.
     *
     * @return array Array of patients info from the cache.
     */
    public function getAllCached(): array
    {
        $patientList = Cache::get($this->allPatientsCacheKey);

        if ($patientList) {
            return $patientList;
        }

        return $this->updateCache();
    }

    /**
     * Create a new patient.
     *
     * @param string first_name
     * @param string last_name
     * @param date birthdate
     * @return int Id of the created patient.
     */
    public function create(
        string $first_name,
        string $last_name,
        string $birthdate
    ) {
        $patient = Patient::create([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'birthdate' => $birthdate,
        ]);

        // Add process handling task to the queue.
        ProcessPatient::dispatch($patient)->onQueue('process_patient');

        $this->updateCache();

        return $patient->id;
    }
}
