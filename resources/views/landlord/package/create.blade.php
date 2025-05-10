@extends('landlord.layout.main') @section('content')

@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{trans('file.Add Package')}}</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                        {!! Form::open(['route' => 'packages.store', 'method' => 'post', 'id' => 'package-form']) !!}
                            <div class="row">
                                <div class="col-md-3 form-group">
                                	<label>{{trans('file.name')}} *</label>
                                    <input type="text" name="name" required class="form-control">
                                </div>
                                <div class="col-md-3 mt-4">
                                    <input type="checkbox" name="is_free_trial" value="1" checked>
                                    <label>{{trans('file.Free Trial')}}</label>
                                </div>
                                <div class="col-md-3 form-group">
                                	<label>{{trans('file.Monthly Fee')}} *</label>
                                    <input type="number" name="monthly_fee" required class="form-control">
                                </div>
                                <div class="col-md-3 form-group">
                                	<label>{{trans('file.Yearly Fee')}} *</label>
                                    <input type="number" name="yearly_fee" required class="form-control">
                                </div>
                                <div class="col-md-3 form-group">
                                	<label>{{trans('file.Number of Warehouses')}}</label>
                                    <input type="number" name="number_of_warehouse" class="form-control" value="0" required>
                                    <p>0 = {{trans('file.Infinity')}}</p>
                                </div>
                                <div class="col-md-2 form-group">
                                	<label>{{trans('file.Number of Products')}}</label>
                                    <input type="number" name="number_of_product" class="form-control" value="0" required>
                                    <p>0 = {{trans('file.Infinity')}}</p>
                                </div>
                                <div class="col-md-2 form-group">
                                	<label>{{trans('file.Number of Invoices')}}</label>
                                    <input type="number" name="number_of_invoice" class="form-control" value="0" required>
                                    <p>0 = {{trans('file.Infinity')}}</p>
                                </div>
                                <div class="col-md-3 form-group">
                                	<label>{{trans('file.Number of User Account')}}</label>
                                    <input type="number" name="number_of_user_account" class="form-control" value="0" required>
                                    <p>0 = {{trans('file.Infinity')}}</p>
                                </div>
                                <div class="col-md-2 form-group">
                                	<label>{{trans('file.Number of Employees')}}</label>
                                    <input type="number" name="number_of_employee" class="form-control" value="0" required>
                                    <p>0 = {{trans('file.Infinity')}}</p>
                                </div>
                                <div class="col-md-6 form-group">
                                	<label>{{trans('file.Features')}}</label>
                                	<ul style="list-style-type: none; margin-left: -30px;">
                                        @foreach ($features as $key => $feature)
                                        <li><input type="checkbox" class="features" name="features[]" value="{{ $key }}" {{ $feature['default'] ? 'checked disabled' : '' }}>&nbsp; {{$feature['name']}}</li>
                                        @endforeach
                                	</ul>
                                </div>
                                <input type="hidden" name="permission_id">
                                <div class="col-md-12 mt-2">
                                    <button type="submit" class="btn btn-primary">{{trans('file.submit')}}</button>
                                </div>
                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script type="text/javascript">
    $("ul#package").siblings('a').attr('aria-expanded','true');
    $("ul#package").addClass("show");
    $("ul#package #package-create-menu").addClass("active");

    $(".features").on("change", function() {
        setPermission();
    });

    function setPermission() {
        var features = @json($features);
        permission_ids = '';
        $(".features").each(function(index) {
            if ($(this).is(':checked')) {
                permission_ids += features[$(this).val()]['permission_ids'];
            }
        });
        if(permission_ids)
            permission_ids = permission_ids.substring(0, permission_ids.length - 1);
        $("input[name=permission_id]").val(permission_ids);
    }

    $(document).on('submit', '#package-form', function(e) {
	    $(".features").prop("disabled", false);
	});
</script>
@endpush
