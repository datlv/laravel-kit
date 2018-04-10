@extends('kit::backend.layouts.master')

@section('content')
<div class="row">
    <div class="col-lg-6">
        <div class="ibox">
            <div class="ibox-title">
                <h5><i class="fa fa-info-circle"></i> {{trans('backend.system_information')}}</h5>
            </div>
            <div class="ibox-content">
                <dl class="dl-horizontal">
                    <dt>PHP version:</dt> <dd><span class="text-warning">{{phpversion()}}</span></dd>
                    <dt>MySql version:</dt> <dd><span class="text-success">{{DB::connection()->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION)}}</span></dd>
                    <dt>Laravel version:</dt> <dd><span class="text-navy">{{app()->version()}}</span></dd>
                    <dt>Server OS:</dt> <dd><span class="text-danger">{{php_uname("s")}}</span> {{php_uname("r")}}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
