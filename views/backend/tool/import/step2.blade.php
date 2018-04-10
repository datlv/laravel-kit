<?php
/** @var \Datlv\Kit\Extensions\Importer $importer */
/** @var array $data */
/** @var string $resource */
$columns = $importer->columnNames();
$hidden = $importer->hidden();
$cols = '';
$toggle_btns = '';
foreach ($columns as $column) {
    if (in_array($column, $hidden)) {
        $visible = " data-visible=false";
        $type = 'default';
    } else {
        $visible = '';
        $type = 'success';
    }
    $cols .= "<th data-name='{$column}'{$visible}>{$column}</th>";
    $toggle_btns .= "<span class='label label-{$type}'>{$column}</span>";
}
?>

@extends('kit::backend.layouts.master')

@section('content')
    <div id="page-import">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                {!! Form::open(['url' =>$url, 'method' => 'post']) !!}
                {!! Form::hidden('filename', $importer->filename()) !!}
                <div class="row">
                    <div class="col-sm-6">
                        <a href="{{route("backend.{$resource}.index")}}"
                           class="btn btn-white btn-block">{{trans('common.cancel')}}</a>
                    </div>
                    <div class="col-sm-6">
                        {!! Form::submit(trans('kit::import.import'), ['class' => 'btn btn-primary btn-block']) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <div class="toggle-column">Toggle column: {!! $toggle_btns !!}</div>
        <div class="ibox ibox-table">
            <div class="ibox-title">
                <h5>{{trans('kit::import.valid_data')}}</h5>
            </div>
            <div class="ibox-content">
                <table id="data" class="table table-striped table-hover table-condensed table-bordered">
                    <thead>
                    <tr>{!! $cols !!}</tr>
                    </thead>
                    <tfoot>
                    <tr>{!! $cols !!}</tr>
                    </tfoot>
                    <tbody>
                    @foreach($data as $row)
                        <tr>
                            <td>{!! implode('</td><td>', $row) !!}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(function () {
            var table = $('#data').DataTable({
                scrollY: "500px",
                scrollX: true,
                scrollCollapse: true,
                paging: false,
                responsive: false
            });
            $('.toggle-column span').click(function () {
                // Get the column API object
                var column = table.column($(this).text() + ':name');
                $(this).toggleClass('label-success').toggleClass('label-default');
                // Toggle the visibility
                column.visible(!column.visible());
            });
        });
    </script>
@endpush
