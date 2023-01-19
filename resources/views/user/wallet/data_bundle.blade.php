@extends('layouts.main.master')
@section('content')
  <!-- Page Content -->
<div class="content">
  <div class="block block-rounded">
    <div class="block-header block-header-default">
      <h3 class="block-title">DataBundle</h3>
    </div>
  <div class="block-content">
 
    <form class="js-validation-reminder" action="{{ route('buy.databundle') }}" method="POST">
        @csrf
      <!-- Text -->

      <div class="mb-2 text-center content-heading mb-4">
        <p class="text-uppercase fw-bold fs-sm text-muted">Buy Databundle</p>
        <p class="link-fx fw-bold fs-1">
          &#8358;{{ number_format(auth()->user()->wallet->balance) }}
        </p>
        <p>Wallet Balance</p>
      </div>

      <div class="row">
        <div class="col-lg-3"></div>
          <div class="col-lg-6">
            @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
            @endif

            @if (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="mb-4">
              <div class="input-group">
                {{-- <span class="input-group-text">
                  &#8358;
                </span> --}}
                {{-- <input type="text" class="form-control text-center" id="example-group1-input3" name="example-group1-input3" placeholder="00"> --}}
                {{-- <input type="number" class="form-control @error('amount') is-invalid @enderror" id="reminder-credential" min="50" max="100" name="amount" value="{{ old('amount') }}" placeholder="Enter Amount" required> --}}
                {{-- <span class="input-group-text">,00</span> --}}

                <select class="form-control" required name="name">
                    @foreach ($databundles as $data)
                        <option value="{{ $data->name }}:{{ $data->amount }}">{{ $data->name }} @ &#8358;{{ number_format($data->amount) }}</option>
                    @endforeach
                   
                </select>
              </div>
            </div>

            <div class="mb-4">
              <div class="input-group">
                <span class="input-group-text">
                  <i class="fa fa-phone"></i>
                </span>
                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="reminder-credential" name="phone" value="{{ old('phone') }}" placeholder="Enter phone Number" required>
                {{-- <input type="text" class="form-control" id="example-group1-input1" name="example-group1-input1"> --}}
              </div>
            </div>
            <div class="text-center mb-4">
              <button type="submit" class="btn btn-primary">
                <i class="fa fa-fw fa-reply opacity-50 me-1"></i> Buy Databundle
              </button>

            </div>
          </div>
          <div class="col-lg-3"></div>
       
      </div>
      <!-- END Text -->
    </form>
  </div>
</div>

@endsection

@section('script')
 <!-- Page JS Plugins -->
 <script src="{{ asset('src/assets/js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>

 <!-- Page JS Code -->
 <script src="{{ asset('src/assets/js/pages/op_auth_reminder.min.js') }}"></script>
@endsection