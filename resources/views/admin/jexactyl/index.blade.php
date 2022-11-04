@extends('layouts.admin')
@include('partials/admin.jexactyl.nav', ['activeTab' => 'index'])

@section('title')
    Jexactyl設定
@endsection

@section('content-header')
    <h1>Jexactyl設定<small>パネルにJexactyl固有の設定を行います。</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li class="active">Jexactyl</li>
    </ol>
@endsection

@section('content')
    @yield('jexactyl::nav')
    <div class="row">
        <div class="col-xs-12">
            <div class="box
                @if($version->isLatestPanel())
                    box-success
                @else
                    box-danger
                @endif
            ">
                <div class="box-header with-border">
                    <i class="fa fa-code-fork"></i> <h3 class="box-title">ソフトウェアリリース<small>Jexactylが最新であることを確認する。</small></h3>
                </div>
                <div class="box-body">
                    @if ($version->isLatestPanel())
                    Jexactylが動作しています。 <code>{{ config('app.version') }}</code>. 
                    @else
                    Jexactylは最新のものではありません。 <code>{{ config('app.version') }} (current) -> <a href="https://github.com/jexactyl/jexactyl/releases/v{{ $version->getPanel() }}" target="_blank">{{ $version->getPanel() }}</a> (latest)</code>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="col-xs-12 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon"><i class="fa fa-server"></i></span>
                    <div class="info-box-content" style="padding: 23px 10px 0;">
                        <span class="info-box-text">総サーバー数</span>
                        <span class="info-box-number">{{ count($servers) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon"><i class="fa fa-wifi"></i></span>
                    <div class="info-box-content" style="padding: 23px 10px 0;">
                        <span class="info-box-text">Total Allocations</span>
                        <span class="info-box-number">{{ $allocations }}</span>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon"><i class="fa fa-pie-chart"></i></span>
                    <div class="info-box-content" style="padding: 23px 10px 0;">
                        <span class="info-box-text">総RAM使用量</span>
                        <span class="info-box-number">{{ $used['memory'] }} MB of {{ $available['memory'] }} MB</span>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon"><i class="fa fa-hdd-o"></i></span>
                    <div class="info-box-content" style="padding: 23px 10px 0;">
                        <span class="info-box-text">ディスクの総使用量</span>
                        <span class="info-box-number">{{ $used['disk'] }} MB of {{ $available['disk'] }} MB </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <i class="fa fa-bar-chart"></i> <h3 class="box-title">リソース使用量 <small>使用されているリソースの総量が一目でわかる。</small></h3>
                </div>
                <div class="box-body">
                    <div class="col-xs-12 col-md-4">
                        <canvas id="servers_chart" width="100%" height="50">
                            <p class="text-muted">このグラフのデータはありません。</p>
                        </canvas>
                    </div>
                    <div class="col-xs-12 col-md-4">
                        <canvas id="ram_chart" width="100%" height="50">
                            <p class="text-muted">このグラフのデータはありません。</p>
                        </canvas>
                    </div>
                    <div class="col-xs-12 col-md-4">
                        <canvas id="disk_chart" width="100%" height="50">
                            <p class="text-muted">このグラフのデータはありません。</p>
                        </canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer-scripts')
    @parent
    {!! Theme::js('vendor/chartjs/chart.min.js') !!}
    {!! Theme::js('js/admin/statistics.js') !!}
@endsection
