@extends('layouts.admin')
@include('partials/admin.users.nav', ['activeTab' => 'overview', 'user' => $user])

@section('title')
    Manager User: {{ $user->username }}
@endsection

@section('content-header')
    <h1>{{ $user->name_first }} {{ $user->name_last}}<small>{{ $user->username }}</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">管理者</a></li>
        <li><a href="{{ route('admin.users') }}">ユーザー</a></li>
        <li class="active">{{ $user->username }}</li>
    </ol>
@endsection

@section('content')
    @yield('users::nav')
    <div class="row">
        <form action="{{ route('admin.users.view', $user->id) }}" method="post">
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">プロフィール</h3>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="email" class="control-label">メールアドレス</label>
                            <div>
                                <input type="email" name="email" value="{{ $user->email }}" class="form-control form-autocomplete-stop">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="registered" class="control-label">ユーザー名</label>
                            <div>
                                <input type="text" name="username" value="{{ $user->username }}" class="form-control form-autocomplete-stop">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="registered" class="control-label">クライアント名</label>
                            <div>
                                <input type="text" name="name_first" value="{{ $user->name_first }}" class="form-control form-autocomplete-stop">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="registered" class="control-label">クライアントの姓</label>
                            <div>
                                <input type="text" name="name_last" value="{{ $user->name_last }}" class="form-control form-autocomplete-stop">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">既定の言語設定</label>
                            <div>
                                <select name="language" class="form-control">
                                    @foreach($languages as $key => $value)
                                        <option value="{{ $key }}" @if($user->language === $key) selected @endif>{{ $value }}</option>
                                    @endforeach
                                </select>
                                <p class="text-muted"><small>このユーザーのパネルをレンダリングするときに使用するデフォルトの言語。</small></p>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        {!! csrf_field() !!}
                        {!! method_field('PATCH') !!}
                        <input type="submit" value="Update User" class="btn btn-primary btn-sm">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">パスワード</h3>
                    </div>
                    <div class="box-body">
                        <div class="alert alert-success" style="display:none;margin-bottom:10px;" id="gen_pass"></div>
                        <div class="form-group no-margin-bottom">
                            <label for="password" class="control-label">パスワード<span class="field-optional"></span></label>
                            <div>
                                <input type="password" id="password" name="password" class="form-control form-autocomplete-stop">
                                <p class="text-muted small">パスワードを変更しない場合は、空白にしてください。パスワードが変更されても、ユーザーには何も通知されません。</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">権限設定</h3>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="root_admin" class="control-label">管理者</label>
                            <div>
                                <select name="root_admin" class="form-control">
                                    <option value="0">@lang('strings.no')</option>
                                    <option value="1" {{ $user->root_admin ? 'selected="selected"' : '' }}>@lang('strings.yes')</option>
                                </select>
                                <p class="text-muted"><small>この設定を「Yes」にすると、ユーザーは完全な管理者権限を持つようになります。</small></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="col-xs-12">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">ユーザー削除</h3>
                </div>
                <div class="box-body">
                    <p class="no-margin">このアカウントを削除するには、このアカウントに関連するサーバーがない必要があります。</p>
                </div>
                <div class="box-footer">
                    <form action="{{ route('admin.users.view', $user->id) }}" method="POST">
                        {!! csrf_field() !!}
                        {!! method_field('DELETE') !!}
                        <input id="delete" type="submit" class="btn btn-sm btn-danger pull-right" {{ $user->servers->count() < 1 ?: 'disabled' }} value="Delete User" />
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
