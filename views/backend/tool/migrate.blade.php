@extends('kit::backend.layouts.master')
@section('content')
    @if($migrated)
        <div class="alert alert-warning">
            {{count($migrated)}} Record migrated!
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <div class="ibox ibox-table">
        <div class="ibox-title">
            <h5>Tables:
                @foreach($tables as $t)
                    <a href="{{route($route_prefix.'backend.tools.migrate', ['from' => $from, 'table' => $t, 'limit' => $limit])}}"
                       class="btn btn-xs btn-{{$t == $table ? 'primary': 'white'}}">{{$t}}</a>
                @endforeach
            </h5>
            <div class="buttons">
                {!! Html::linkButton(
                route($route_prefix.'backend.tools.migrate', ['from' => $from, 'table' => $t, 'limit' => $limit, 'ids' => 'all']),
                trans('kit::migrate.migrate_all'), ['class'=>'migrate_all', 'type'=>'warning', 'size'=>'xs', 'icon' => 'fa-check']) !!}
            </div>
        </div>
        <div class="ibox-content">
            <table class="table table-bordered">
                <tr>
                    <th class="min-width">#</th>
                    <th>Content</th>
                    <th class="min-width"></th>
                </tr>
                @foreach($models as $i => $model)
                    <tr>
                        <td class="min-width">{{$i +1}}</td>
                        <td>
                            <table class="table table-detail table-hovered">
                                @foreach($model as $attr => $value)
                                    <tr>
                                        <td>{{$attr}}</td>
                                        <td>{{mb_string_limit($value, 300)}}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                        <td class="min-width">
                            <a href="{{route($route_prefix.'backend.tools.migrate', ['from' => $from, 'table' => $t, 'limit' => $limit, 'ids' => $model['id']])}}"
                               class="btn btn-xs btn-success"><i class="fa fa-plus"></i></a>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@stop

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function () {

        });
    </script>
@endpush

