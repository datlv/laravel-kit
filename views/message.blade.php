@extends('kit::backend.layouts.basic')

@section('heading', (isset($module) ? "$module / ":'').trans('common.message'))
@section('classes', 'col-md-6 col-md-offset-3 messages-page')
@section('content')
    <div class="text-center alert alert-{{$type}}">
        {!!$content!!}
    </div>
    @if(isset($buttons))
        <div class="text-center buttons">
            @foreach($buttons as $button)
                @if($button)
                    {!! Html::linkButton($button) !!}
                @endif
            @endforeach
        </div>
    @endif
    <div class="hr-line-dashed"></div>
@stop
