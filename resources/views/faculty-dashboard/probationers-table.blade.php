<?php
$batch_id   = $request->batch_id;
if(empty($batch_id)) {
    echo "Please select Batch";
    die();
}
$Probationers   = \App\Models\probationer::where('batch_id', $batch_id)->orderBy('Name', 'asc')->get();
?>

@if(count($Probationers) > 0)
<div class="listdetails">
    <div class="squadlisthead">
        <div class="row">
            <div class="group col-md-2">
                <img src="{{ asset('images/Group.png') }}">
            </div>
            <div class="col-md-10">

            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table" id="probationersTable">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Probationer</th>
                    <th>Roll Number</th>
                    <th>Squad</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $sl     = 1;

                    foreach ($Probationers as $Probationer) {

                        $pb_id      = $Probationer->probationer_id;
                        $pb_name    = $Probationer->Name;
                        $roll       = $Probationer->RollNumber;
                        $squad_id   = $Probationer->squad_id;

                        $squad_num  = squad_number($squad_id);
                        ?>

                        <tr>
                            <td>{{ $sl }}</td>
                            <td>{{ $pb_name }}</td>
                            <td>{{ $roll }}</td>
                            <td>{{ $squad_num }}</td>
                        </tr>

                        <?php
                        $sl++;
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>
@else
{{ 'No probationers found' }}
@endif
