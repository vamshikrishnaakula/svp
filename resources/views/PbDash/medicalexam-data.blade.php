{{-- This file included in Hospitalization / Medical Examination screen --}}

<div class="row mt-5">
    <div class="col-md-6 medical-data-table">
        <table >
            <tbody>
                <tr>
                    <td>Temperature</td>
                    <td>: {{ $medical_exam->temperature }}</td>
                </tr>
                <tr>
                    <td>Antigen Test</td>
                    <td>: {{ $medical_exam->antigentest }}</td>
                </tr>
                <tr>
                    <td>RTPCR</td>
                    <td>: {{ $medical_exam->rtpcr }}</td>
                </tr>
                <tr>
                    <td>Hemoglobin</td>
                    <td>: {{ $medical_exam->haemoglobin }}</td>
                </tr>
                <tr>
                    <td>Calcium</td>
                    <td>: {{ $medical_exam->calcium }}</td>
                </tr>
                <tr>
                    <td>Vitamin D</td>
                    <td>: {{ $medical_exam->vitamind }}</td>
                </tr>
                <tr>
                    <td>Vitamin B12</td>
                    <td>: {{ $medical_exam->vitaminb12 }}</td>
                </tr>
               
            </tbody>
        </table>
    </div>
    <div class="col-md-6 medicaldatatext">
        <div class="row">
            <label class="col-sm-4">Pre-existing injury:</label>
            <div class="col-sm-8 p-0 mgn-left">
                <p>{{ $medical_exam->preexistinginjury }}</p>
            </div>
        </div>
           <div class="mt-4"><p class="d-inline">Family members ever tested Covid +ve </p> <div class="d-inline pl-3">: {{ $medical_exam->covid }}</div></div>
    </div>
</div>
