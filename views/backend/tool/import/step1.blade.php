<?php
/** @var Illuminate\Support\ViewErrorBag $errors */
/** @var \Datlv\Kit\Extensions\Importer $importer */
?>
@extends('kit::backend.layouts.master')
@section('content')
    <div class="row">
        <div id="page-import">
            <div class="col-md-8 col-lg-6 col-md-offset-2 col-lg-offset-3">
                @if(isset($message))
                    <div class="alert alert-warning">{!! $message !!}</div>
                @endif
                @if($errors->has('file'))
                    <div class="alert alert-danger">{{$errors->first('file')}}</div>
                @endif
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{trans('kit::import.upload_data_file')}}</h5>
                    </div>
                    <div class="ibox-content">
                        {!! Form::open(['files' => true, 'url' =>$url, 'method' => 'post']) !!}
                        <div class="row">
                            <div class="col-sm-9">
                                {!! Form::fileinput('file') !!}
                            </div>
                            <div class="col-sm-3">
                                {!! Form::submit(trans('kit::import.import'), ['class' => 'btn btn-success btn-block']) !!}
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="alert alert-info">
        <h3><i class="fa fa-info-circle"></i> {{trans('kit::import.validation_rules')}}</h3>
        {!! $importer->rules_hint() !!}
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(function () {

        });
    </script>
@endpush
