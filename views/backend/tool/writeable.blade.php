@extends('kit::backend.layouts.master')

@section('content')
    <div class="ibox ibox-table">
        <div class="ibox-title">
            <h5>Root: <span class="text-navy">{{ $base_path }}</span></h5>
            <div class="buttons">
                {!! Html::linkButton('#', trans('backend.fix_writeable'), ['class' => $ok ? 'disabled' : '', 'id' => 'action-fix-writeable', 'size' => 'xs', 'icon' => 'wrench', 'type' => 'danger']) !!}
            </div>
        </div>
        <div class="ibox-content">
            <table class="table table-striped table-bordered table-hover table-list">
                <thead>
                <tr>
                    <th class="min-width text-right">#</th>
                    <th class="min-width">{{ trans('common.dir') }}</th>
                    <th>Full Path</th>
                    <th class="min-width">Tồn tại</th>
                    <th class="min-width">Quyền ghi</th>
                </tr>
                </thead>
                <tbody>
                <?php $i = 1; ?>
                @foreach ($paths as $path => $info)
                    <tr>
                        <td class="min-width text-right">{{$i++}}</td>
                        <td class="min-width">{{$info['name']}}</td>
                        <td><strong>{{$path}}</strong></td>
                        <td class="min-width text-center">{!! Html::yesNo($info['exist'])!!}</td>
                        <td class="min-width text-center">{!! Html::yesNo($info['writeable'])!!}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    {!! Form::open(['id'=>'fix_writeable']) !!}
    {!! Form::close() !!}
@stop

@push('scripts')
<script type="text/javascript">
    $('#action-fix-writeable').click(function (e) {
        e.preventDefault();
        if (!$(this).hasClass('disabled')) {
            $('form#fix_writeable').submit();
        }
    });
</script>
@endpush
