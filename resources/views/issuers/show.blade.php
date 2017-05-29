@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Nhà phát hành
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('issuers.show_fields')
                    <a href="{!! route('issuers.index') !!}" class="btn btn-default">Quay lại</a>
                </div>
            </div>
        </div>
    </div>
@endsection
