<?php
$batch_id   = $request->batch_id;
if(empty($batch_id)) {
    echo "Please select Batch";
    die();
}

$Squads     = App\Models\Squad::where('Batch_Id', $batch_id)->orderBy('SquadNumber', 'asc')->get();
?>

@if(count($Squads) > 0)
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
        <table class="table" id="squadlist" name="squadlist">
            <thead>
                <tr>
                    <th>S.NO</th>
                    <th>Squad Number</th>
                    <th>Drill Inspector</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="squadlistbody">
                <?php
                    $sl     = 1;
                ?>
                @foreach ($Squads as $Squad)
                <?php
                    $squad_id   = $Squad->id;
                    $di_id      = $Squad->DrillInspector_Id;
                    $di_name    = App\Models\User::where('id', $di_id)->value('name');
                ?>
                <tr>
                    <td>{{ $sl }}</td>
                    <td>{{ $Squad->SquadNumber }}</td>
                    <td>{{ $di_name }}</td>
                    <td>
                        <a href="#" id="probationerdata1" onclick="window.get_squad_probationers('{{ $squad_id }}');return false;"><img src="/images/view.png"><span></span></a>
                    </td>
                </tr>
                <?php $sl++; ?>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
    {{ 'No squads found' }}
@endif
