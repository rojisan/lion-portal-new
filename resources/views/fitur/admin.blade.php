@extends('parent.master')
@section('extend-css')
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
@endsection
@section('body')
<!-- Site wrapper -->
<div class="content-wrapper" style="min-height: 278px;">
    <!-- Navbar -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>User Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">

                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Users</h3>
                            <div class="float-sm-right">
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#modal-add-user">+ Tambah User</button>
                            </div>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible alert-message">
                                    <i class="icon fas fa-check"></i>
                                    {{ session('success') }}
                                </div>
                            @endif
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible alert-message">
                                    <i class="icon fas fa-ban"></i>
                                    {{ session('error') }}
                                </div>
                            @endif

                            @if($errors->any())
                                <div class="alert alert-danger alert-message">
                                    {{ $errors->first() }}
                                </div>
                            @endif
                            <table id="user_list" class="table table-bordered table-hover display nowrap" width="100%">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>User Type</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <div id="modal-add-user" class="modal fade show" aria-modal="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add User</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="{{ route('add.admin') }}" method="post" name="user">
                    @csrf
                    <div class="modal-body">
                        {{-- <div class="form-group">
                            <label class="form-check-label" for="userFullName" disabled>Full name</label>
                            <input type="text" class="form-control" id="userFullName">
                        </div> --}}
                        <div class="form-group">
                            <label class="form-check-label" for="userEmail" disabled>Email</label>
                            <input type="text" name="adm_email" class="form-control" id="userEmail">
                        </div>
                        <div class="form-group">
                            <label class="form-check-label" for="username" disabled>Role</label>
                            <select name="user_type" id="" class="custom-select">                            
                                <option value="superadmin">Superadmin</option>
                                <option value="admin">Admin</option>
                                <option value="krani">Krani</option>
                                <option value="verify">Verify</option>
                                <option value="customer">Customer</option>
                                <option value="master">Master</option>
                                <option value="invoice">Invoice</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-check-label" for="username" disabled>Username</label>
                            <input type="text" name="adm_name" class="form-control" id="username">
                        </div>
                        <div class="form-group">
                            <label class="form-check-label" for="userPassword" disabled>Password</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="password" name="adm_pswd" class="form-control" id="userPassword" maxlength="12" >
                                </div>
                                <div class="input-group-append">
                                    <span class="input-group-text" id="showPassword"><i
                                            class="fa fa-solid fa-eye-slash"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" checked="" value="active" name="deskripsi">
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" id="save-btn" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
    </div>
    <!-- /.content -->

    <div id="modal-update-user" class="modal fade show" aria-modal="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Update User</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="{{ route('update.admin') }}" method="post" name="update-user">
                    @csrf
                    <div class="modal-body">
                        {{-- <div class="form-group">
                            <label class="form-check-label" for="userFullName" disabled>Full name</label>
                            <input type="text" class="form-control" id="userFullName">
                        </div> --}}
                        <div class="form-group">
                            <label class="form-check-label" for="userEmail" disabled>Email</label>
                            <input type="text" name="adm_email" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-check-label" for="username" disabled>Role</label>
                            <select name="user_type" class="custom-select">
                            <option value="superadmin">Superadmin</option>
                                <option value="admin">Admin</option>
                                <option value="krani">Krani</option>
                                <option value="verify">Verify</option>
                                <option value="customer">Customer</option>
                                <option value="master">Master</option>
                                <option value="invoice">Invoice</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-check-label" for="username" disabled>Username</label>
                            <input type="text" name="adm_name" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-check-label" for="userPassword" disabled>Password</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="password" name="adm_pswd" class="form-control" value="*********" disabled>
                                </div>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-default" name="reset-password">Reset Password</button>
                                </div>
                            </div>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" checked="" value="active" name="deskripsi">
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" id="update-btn" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
    </div>
    <!-- /.content -->

    <!-- Modal delete -->
    <div class="modal fade show" id="modal-delete-user" aria-modal="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Delete User</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="{{route('delete.admin')}}" method="post">
                    @csrf
                    <input type="hidden" id="delete-user-id" name="id"/>
                    <div class="modal-body">
                        <p>Yakin ingin menghapus <span class="text-bold" id="delete-user-name"></span></p>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-danger">Ya, hapus</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
</div>
<!-- ./wrapper -->
@endsection

@section('extend-js')
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script>
    $('.nav-link.active').removeClass('active');
    $('#m-user-manage').addClass('active');
    $(function () {
        $('#showPassword').on('click', function () {
            if ($('#userPassword').attr('type') === "password") {
                $('#showPassword').html('<i class="fa fa-solid fa-eye"></i>')
                $('#userPassword').attr('type', 'text');
            } else {
                $('#showPassword').html('<i class="fa fa-solid fa-eye-slash"></i>')
                $('#userPassword').attr('type', 'password');
            }
        });

        $('#save-btn').on('click', function () {
            console.log("clicked");
            var $inputs = $('form[name="user"] :input');
            $inputs.each(function () {
                if ($(this).attr('type') !== "button" && $(this).attr('type') !== undefined) {
                    if ($(this).val() === "") {
                        if ($(this).attr('name') === "adm_pswd") {
                            $(this).parent().parent().parent().append(
                                '<span class="text-danger">Invalid input</span>');
                        } else {
                            $(this).parent().append(
                                '<span class="text-danger">Invalid input</span>');
                        }
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                        if ($(this).attr('name') === "adm_pswd") {
                            $(this).parent().parent().parent().children("span").remove();
                        } else {
                            $(this).parent().children("span").remove();
                        }
                    }
                }
            });
            var $form_valid = $('form[name="user"] :input.is-invalid');
            if ($form_valid.length === 0) {
                $('form[name="user"]').submit();
            }
        });

        $(document).on('click', '.delete', function () {
            $('#delete-user-name').text($(this).attr("data-name"));
            $('#delete-user-id').val($(this).attr("data-id"));
            $('#modal-delete-user').modal('show');
        });

        $(document).on('click', '.edit', function() {
            var user_id = $(this).attr('data-id');
            var user_name  =$(this).attr('data-name');
            var user_email =$(this).attr('data-email');
            var user_status = $(this).attr('data-status');
            var user_type = $(this).attr('data-ws');
            var $modal = $('#modal-update-user');
            var $form = $modal.find('form[name="update-user"]');
            $form.find('input[name="adm_email"]').val(user_email);
            $form.find('input[name="adm_name"]').val(user_name);
            var $option = $form.find('select[name="user_type"]').children();
            $option.each(function() {
                if($(this).val() === user_type) {
                    $(this).attr('selected', true);
                } else {
                    $(this).attr('selected', false);
                }
            });
            $form.find('button[name="reset-password"]').on('click', function() {
                $form.find('input[name="adm_pswd"]').attr('disabled', false);
                $form.find('input[name="adm_pswd"]').val('');
            })
            $modal.modal('show');
        });

        $('#modal-update-user form[name="update-user"] button#update-btn').on('click',
        function() {
            var $inputs = $('#modal-update-user form[name="update-user"] :input');
            $inputs.each(function () {
                if ($(this).attr('type') !== "button" && $(this).attr('type') !== undefined) {
                    if ($(this).val() === "") {
                        if ($(this).attr('name') === "adm_pswd") {
                            $(this).parent().parent().parent().append(
                                '<span class="text-danger">Invalid input</span>');
                        } else {
                            $(this).parent().append(
                                '<span class="text-danger">Invalid input</span>');
                        }
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                        if ($(this).attr('name') === "adm_pswd") {
                            $(this).parent().parent().parent().children("span").remove();
                        } else {
                            $(this).parent().children("span").remove();
                        }
                    }
                }
            });
            var $form_valid = $('#modal-update-user form[name="update-user"] :input.is-invalid');
            if ($form_valid.length === 0) {
                $('#modal-update-user form[name="update-user"]').submit();
            }
        });

        var table = $('#user_list').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            searching: false,
            ajax: "{{ route('data.list') }}",
            columns: [
                { data: 'adm_name', name: 'name' },
                { data: 'user_type', name: 'usertype' },
                { data: 'adm_email', name: 'email' },
                { data: 'deskripsi', name: 'desc' },
                { data: 'action', name: 'action' },
            ],
            oLanguage: {
				"sLengthMenu": "Tampilkan _MENU_ data",
				"sProcessing": "Memproses...",
				"sSearch": "Cari data:",
				"sInfo": "Menampilkan _START_ - _END_ dari _TOTAL_ data" 	
			},
            oLanguage: {
				"sLengthMenu": "Tampilkan _MENU_ data",
				"sProcessing": "Memproses...",
				"sSearch": "Cari data:",
				"sInfo": "Menampilkan _START_ - _END_ dari _TOTAL_ data" 	
			},
        });
    });
</script>

<script>
    window.setTimeout(function() {
    $(".alert-message").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); 
    });
}, 5000);
</script>
@endsection
