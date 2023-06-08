<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Styles -->
        <style>
            .patient-card {
                padding: 10px;
                border: 1px solid #ccc;
            }
        </style>
    </head>
    <body>
        <div class="patient-list">
          @foreach ($patientList as $patient)
            <div class="patient-card">
               <div>
                    {{ __('patient.title') }}:
                    {{$patient['name']}}
                </div>
               <div>
                    {{ __('patient.birthdate') }}:
                    {{$patient['birthdate']}}
                </div>
               <div>
                    {{ __('patient.age') }}:
                    {{$patient['age']}}
                </div>
            </div>
          @endforeach
        </div>
    </body>
</html>
