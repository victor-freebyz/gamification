@extends('layouts.main.master')

@section('content')


<div class="bg-body-light">
    <div class="content content-full">
      <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
        <h1 class="flex-grow-1 fs-3 fw-semibold my-2 my-sm-3">Preferences Management</h1>
        <nav class="flex-shrink-0 my-2 my-sm-0 ms-sm-3" aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item active" aria-current="page">Preferencesa</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>

  <!-- Page Content -->
  <div class="content">
    <div class="block block-rounded">
        <div class="block-content">
            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif
        <form action="{{ url('preferences') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row push">
                <div class="col-lg-4">
                    <p class="text-muted">
                    Create Preferences
                    </p>
                </div>
                <div class="col-lg-8 col-xl-5">
                    <div class="mb-4">
                    <label class="form-label" for="example-text-input">Name</label>
                    <input type="text" class="form-control" id="example-text-input" name="name" placeholder="Preferences">
                    </div>
                    <div class="mb-4">
                        <button class="btn btn-primary" type="submit">Create</button>
                    </div>
                </div>
            </div>
        </form>
        </div>
    </div>
    <!-- Full Table -->
    <div class="block block-rounded">
      <div class="block-header block-header-default">
        <h3 class="block-title">Preferences List</h3>
        <div class="block-options">
          <button type="button" class="btn-block-option">
            <i class="si si-settings"></i>
          </button>
        </div>
      </div>
      <div class="block-content">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-vcenter js-dataTable-full-pagination">
              <thead>
                  <tr>
                      <th>#</th>
                      <th>Name</th>
                      <th>Action</th>
                      <th>When Created</th>
                  </tr>
              </thead>
              <tbody>
                <?php $i = 1; ?>
                @foreach ($preferences as $pref)
                <tr>
                    <td>{{ $i++ }}. </td>
                    <td>{{ $pref->name }}</td>
                    <td> <button type="button" class="btn btn-alt-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-default-popout-{{ $pref->id }}">Edit</button></td>
                    <td>{{ $pref->created_at }}</td>
                </tr>

                <div class="modal fade" id="modal-default-popout-{{ $pref->id }}" tabindex="-1" role="dialog" aria-labelledby="modal-default-popout" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-popout" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                        <h5 class="modal-title">Edit {{ $pref->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body pb-1">
                            <div class="col-xl-12">
                                <!-- With Badges -->
                                <div class="block block-rounded">
                                  <div class="block-content">
                                    <form action="{{ route('preferences.update', $pref) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="row push">
                                            <div class="col-lg-12">
                                                <div class="mb-4">
                                                <label class="form-label" for="example-text-input">Name</label>
                                                <input type="text" class="form-control" id="example-text-input" name="name" value="{{ $pref->name }}">
                                                </div>
                                                <div class="mb-4">
                                                    <button class="btn btn-primary" type="submit">Update</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                  </div>
                                </div>
                                <!-- END With Badges -->
                              </div>
                            
                        </div>
                        
                        <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-alt-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                    </div>
                </div>
                @endforeach
              </tbody>
            </table>
        </div>
      </div>
    </div>



@endsection