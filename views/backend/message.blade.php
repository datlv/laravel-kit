@extends('kit::backend.layouts.modal')
@section('content')
    <div class="text-center alert alert-{{$type}}">
        {!!$content!!}
    </div>
@stop