@section('jexactyl::nav')
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li @if($activeTab === 'index')class="active"@endif><a href="{{ route('admin.index') }}">ホーム</a></li>
                    <li @if($activeTab === 'theme')class="active"@endif><a href="{{ route('admin.jexactyl.theme') }}">テーマ</a></li>
                    <li @if($activeTab === 'mail')class="active"@endif><a href="{{ route('admin.jexactyl.mail') }}">メール</a></li>
                    <li @if($activeTab === 'advanced')class="active"@endif><a href="{{ route('admin.jexactyl.advanced') }}">高度な設定</a></li>
                    <li style="margin-left: 5px; margin-right: 5px;"><a>-</a></li>
                    <li @if($activeTab === 'store')class="active"@endif><a href="{{ route('admin.jexactyl.store') }}">ストア設定</a></li>
                    <li @if($activeTab === 'registration')class="active"@endif><a href="{{ route('admin.jexactyl.registration') }}">ユーザー登録</a></li>
                    <li @if($activeTab === 'approvals')class="active"@endif><a href="{{ route('admin.jexactyl.approvals') }}">ユーザーの認証</a></li>
                    <li @if($activeTab === 'server')class="active"@endif><a href="{{ route('admin.jexactyl.server') }}">サーバー設定</a></li>
                    <li @if($activeTab === 'referrals')class="active"@endif><a href="{{ route('admin.jexactyl.referrals') }}">アフェリエイト設定</a></li>
                    
                </ul>
            </div>
        </div>
    </div>
@endsection
