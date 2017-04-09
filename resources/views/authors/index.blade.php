@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">@lang('authors.label_author_list')</h1>
        <h1 class="pull-right">
           <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('authors.create') !!}">@lang('buttons.btn_add_new')</a>
        </h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                    @include('authors.table')
            </div>
        </div>
    </div>
@endsection

