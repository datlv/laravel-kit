@extends('kit::backend.layouts.master')
@section('content')
    <div class="ibox ibox-table">
        <div class="ibox-title">
            <div class="ibox-tools buttons">
                {!! Form::select('controller', $all_controllers, $only_controller, ['id'=>'controller', 'class' => 'selectize w-lg']) !!}
            </div>
        </div>
        <div class="ibox-content">
            <table class="table table-hover table-striped table-condensed">
                <thead>
                <tr>
                    <th class="min-width">Methods</th>
                    <th>Path
                    </td>
                    <th class="min-width">Name</th>
                    <th>Action</th>
                    <th>Middleware</th>
                </tr>
                </thead>
                <tbody>
                <?php $methodColours = [
                        'GET'    => 'success',
                        'HEAD'   => 'default',
                        'POST'   => 'primary',
                        'PUT'    => 'warning',
                        'PATCH'  => 'info',
                        'DELETE' => 'danger'
                ]; ?>
                @foreach ($routes as $route)
                    <tr>
                        @if(empty($route['controller']))
                            <td class="min-width">
                                @foreach ($route['methods'] as $method)
                                    <span class="label label-{{ array_get($methodColours, $method) }}">{{ $method }}</span>
                                @endforeach
                            </td>
                            <td>{!! preg_replace('#({[^}]+})#', '<span class="text-warning">$1</span>', $route['uri']) !!}</td>
                            <td class="min-width">{{ $route['name'] }}</td>
                            <td class="text-warning">{{$route['action']}}</td>
                            <td>{{ implode(', ', $route['middleware']) }}
                        @else
                            <td colspan="5" class="text-success bg-success"><strong>{{$route['controller']}}</strong>
                            </td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@push('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#controller').change(function () {
                window.location.href = "{{$url}}" + "?controller=" + $(this).val();
            });
        });
    </script>
@endpush