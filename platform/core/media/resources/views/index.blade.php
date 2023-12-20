@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('head')
    {!! RvMedia::renderHeader() !!}
@endsection

@section('content')
    {!! RvMedia::renderContent() !!}
@endsection

@section('javascript')
    {!! RvMedia::renderFooter() !!}
@endsection
