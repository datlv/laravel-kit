<?php
/** @var string $title */
/** @var string $message */
/** @var string $type */
/** @var string $resource */
?>
@extends('kit::backend.layouts.master')
@section('content')
    <div class="row">
        <div id="page-import">
            <div class="col-md-8 col-lg-6 col-md-offset-2 col-lg-offset-3">
                <div class="alert alert-{{$type}}">{!! $message !!}</div>
                <div class="row">
                    <div class="col-xs-6">
                        <a href="{{route("backend.{$resource}.index")}}"
                           class="btn btn-white btn-block">{{trans('common.manage_object', ['name' => $title])}}</a>
                    </div>
                    <div class="col-xs-6">
                        <a href="{{route('backend.tools.import.step1', compact('resource'))}}"
                           class="btn btn-success btn-block">{{$step1_title}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
