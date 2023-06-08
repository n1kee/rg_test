<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Repositories\PatientRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PatientController extends Controller
{
    public function __construct(
        protected PatientRepository $patientRepository
    ) {
    }

    /**
     * Shows the list of patients.
     */
    public function show(): View
    {
        return view('patients', [
            'patientList' => $this->patientRepository->getAllCached(),
        ]);
    }

    /**
     * Creates a patient.
     */
    public function create(Request $request): Response
    {
        return response($this->patientRepository->create(
            $request->input('first_name'),
            $request->input('last_name'),
            $request->input('birthdate'),
        ));
    }
}
