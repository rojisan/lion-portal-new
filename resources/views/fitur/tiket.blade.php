@extends('parent.master')
@section('extend-css')
<link rel="stylesheet" href="{{ asset('plugins/bootstrap/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('image-upload/image-uploader.min.css') }}">
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
                    <h1>TICKET</h1>
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
                            <div class="float-sm-right">
                                <button type="button" class="btn btn-success" data-toggle="modal"
                                    data-target="#modal-add-ticket">+ New Ticket</button>
                            </div>
                            <div class="row align-items-end">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Requestor :</label>
                                        <div class="input-group value">
                                            <select id="requestor" name="requestor" class="form-control input--style-6" required>
                                            <option value="10"> all</option>
                                                @foreach($usreq as $usreqcode)
                                                <option value="{{ $usreqcode['ID'] }}">{{ $usreqcode['NAME'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label>Ticket No :</label>
                                        <div class="input-group value">
                                            <select id="ticketno" name="ticketno" class="form-control input--style-6" required>
                                            <option value="HLP"> all</option>
                                                @foreach($tick as $tickcode)
                                                <option value="{{ $tickcode['ID'] }}">{{ $tickcode['NAME'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Assign To :</label>
                                        <div class="input-group value">
                                            <select id="assignto" name="assignto" class="form-control input--style-6" required>
                                                <option value="10"> all</option>
                                                @foreach($assn as $assncode)
                                                <option value="{{ $assncode['ID'] }}">{{ $assncode['NAME'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label>Status :</label>
                                        <div class="input-group value">
                                            <select id="status" name="status" class="form-control input--style-6" required>
                                                <option value="SD00"> all</option>
                                                @foreach($stat as $statcode)
                                                <option value="{{ $statcode['ID'] }}">{{ $statcode['NAME'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Date Range:</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="far fa-calendar-alt"></i>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control float-right datepicker"
                                                name="data_date_range">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <button id="ticket" name="ticket" class="ticket btn-submit btn btn-success" >Search</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                       
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible alert-message" >
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
                            <table id="tiket_list" class="table table-bordered table-hover display nowrap" width="100%">
                                <thead>
                                    <tr>
                                        <th>Tiket No</th>
                                        <th>Category</th>
                                        <th>Priority</th>
                                        <th>Requestor</th>
                                        <th>Assigned To</th>
                                        <th>Status</th>
                                        <th>Created On</th>
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
    <div id="modal-add-ticket" class="modal fade show" aria-modal="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">New Ticket</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="{{ route('add-tiket') }}" method="post" enctype="multipart/form-data" name="ticket">
                    @csrf
                    <input type="hidden" id="ticketno" name="ticketno">
                    <input type="hidden" id="statusid" name="statusid">
                    <input type="hidden" id="status" name="status">
                    <input type="hidden" id="roleid" name="roleid">
                    <div class="modal-body">
                        @if(session('roleid') == 'RD004' || session('roleid') == 'RD005' || session('roleid') == 'RD006')
                        <div class="form-group">
                            <div class="name">User Request :</div>
                            <div class="input-group value">
                                <select id="user" name="user" class="form-control input--style-6" required>
                                    <option value=""> Masukkan Pilihan :</option>
                                    @foreach($usreq as $usreqcode)
                                    <option value="{{ $usreqcode['ID'] }}">{{ $usreqcode['NAME'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                        <div class="form-group">
                            <div class="name">Category :</div>
                            <div class="input-group value">
                                <select id="category" name="category" class="form-control input--style-6" required>
                                    <option value=""> Masukkan Pilihan :</option>
                                    @foreach($categ as $categcode)ID
                                    <option value="{{ $categcode['ID'] }}">{{ $categcode['NAME'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="name">Priority :</div>
                            <div class="input-group value">
                                <select id="priority" name="priority" class="form-control input--style-6" required>
                                    <option value=""> Masukkan Pilihan :</option>
                                    @foreach($prior as $priorcode)
                                    <option value="{{ $priorcode['ID'] }}">{{ $priorcode['NAME'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @if(session('roleid') == 'RD006')
                        <div class="form-group">
                            <div class="name">Assigned To :</div>
                            <div class="input-group value">
                                <select id="assignto" name="assignto" class="form-control input--style-6" required>
                                    <option value=""> Masukkan Pilihan :</option>
                                    @foreach($assn as $assncode)
                                    <option value="{{ $assncode['ID'] }}">{{ $assncode['NAME'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                        <div class="form-group">
                            <label class="form-check-label" for="group">Subject</label>
                            <input type="text" name="subject" class="form-control" id="subject" required>
                        </div>
                        <div class="form-group">
                            <label class="form-check-label" for="detail">Detail Issue</label>
                            <textarea type="text" name="detail" class="form-control" id="detail" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-check-label" for="files">File:</label>
                            <input type="file" name="files" id="files" class="form-control">
                        </div>
                        <!-- <div class="input-field">
                            <label class="active">Upload Photos<small>(max size: 1mb)</small></label>
                            <div class="input-images" style="padding-top: .5rem;"></div>
                        </div> -->
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" id="save-btn" class="btn btn-primary">Add Ticket</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
    </div>
    <!-- /.content -->
    <div id="modal-view-user" class="modal fade show" aria-modal="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">View Ticket</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="" method="post" name="view-user">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-check-label" for="id" disabled>ID Requestor</label>
                            <input type="text" name="id" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-check-label" for="ticketno" disabled>Ticket No :</label>
                            <input type="text" name="ticketno" class="form-control" id="ticketno" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-check-label" for="requestor" disabled>User Request :</label>
                            <input type="text" name="requestor" class="form-control" id="requestor" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-check-label" for="category" disabled>Category :</label>
                            <input type="text" name="category" class="form-control" id="category" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-check-label" for="priority" disabled>Priority :</label>
                            <input type="text" name="priority" class="form-control" id="priority" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-check-label" for="subject" disabled>Subject :</label>
                            <input type="text" name="subject" class="form-control" id="subject" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-check-label" for="detail" disabled>Detail Issue :</label>
                            <input type="text" name="detail" class="form-control" id="detail" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-check-label" for="assignto" disabled>Assigned To :</label>
                            <input type="text" name="assignto" class="form-control" id="assignto" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-check-label" for="status" disabled>Status :</label>
                            <input type="text" name="status" class="label-success" id="status" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-check-label" for="upload" disabled>Attachment :</label>
                            <!-- <a href="/download" id="upload" name="upload" class="btn btn-large pull-right"><i class="icon-download-alt"> -->
                            <input type="text" id="upload" name="upload" class="form-control" readonly><a href="{{ url('/download') }}" id="upload" name="upload" >donwload</a>
                        </div>
                        <div class="form-group">
                            <label class="form-check-label" for="approve" disabled>Approve By :</label>
                            <input type="text" name="approve" class="form-control" id="approve" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-check-label" for="created" disabled>Created Ticket :</label>
                            <input type="text" name="created" class="form-control" id="created" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-check-label" for="comment" disabled>Comment</label>
                            <textarea type="text" name="comment" class="form-control" id="comment"></textarea>
                        </div>
                    </div>
                    <!-- <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" id="update-btn" class="btn btn-primary">Save</button>
                    </div> -->
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
    </div>
    <!-- /.content -->
    <div id="modal-update-user"  class="modal fade show"  aria-modal="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Update Ticket</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="{{route('update-tiket')}}" method="post" name='update'>
                    @csrf
                    <input type="hidden" id="update-ticketno" name="ticketno"/>
                    <input type="hidden" id="update-userid" name="userid"/>
                    <input type="hidden" id="update-rejectedby" name="rejectedby"/>
                    <div class="modal-body">
                    <p>Are You Sure ? <span class="text-bold"></span></p>
                        @if(session('roleid') == 'RD006')
                        <div class="form-group">
                            <div class="name">Assigned To :</div>
                            <div class="input-group value">
                                <select id="assignto" name="assignto" class="form-control input--style-6" required>
                                    <option value=""> Masukkan Pilihan :</option>
                                    @foreach($assn as $assncode)
                                    <option value="{{ $assncode['ID'] }}">{{ $assncode['NAME'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group value">
                                <input id="statusid" name="statusid" class="form-control input--style-6" type="hidden" value="SD002">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group value">
                                <input id="status" name="status" class="form-control input--style-6" type="hidden">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group value">
                                <input id="approvedbyit_date" name="approvedbyit_date" class="form-control input--style-6" type="hidden" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group value">
                                <input id="approvedbyit" name="approvedbyit" class="form-control input--style-6" type="hidden" value="{{ session('userid') }}">
                            </div>
                        </div>
                        @endif
                        @if(session('roleid') == 'RD002')
                        <div class="form-group">
                            <div class="input-group value">
                                <input id="statusid" name="statusid" class="form-control input--style-6" type="hidden" value="SD001">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group value">
                                <input id="approvedby1_date" name="approvedby1_date" class="form-control input--style-6" type="hidden" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group value">
                                <input id="approvedby1" name="approvedby1" class="form-control input--style-6" type="hidden" value="{{ session('userid') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group value">
                                <input id="assignto" name="assignto" class="form-control input--style-6" type="hidden" value="101943">
                            </div>
                        </div>
                        @endif
                        @if(session('roleid') == 'RD004' || session('roleid') == 'RD005')
                        <div class="form-group">
                            <div class="input-group value">
                                <input id="statusid" name="statusid" class="form-control input--style-6" type="hidden" value="SD002">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group value">
                                <input id="approvedbyit" name="approvedbyit" class="form-control input--style-6" type="hidden" value="{{ session('mgrid') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group value">
                                <input id="approvedby1" name="approvedby1" class="form-control input--style-6" type="hidden" value="{{ session('mgrid') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group value">
                                <input id="assignto" name="assignto" class="form-control input--style-6" type="hidden" value="{{ session('userid') }}">
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Yes</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div id="modal-reject-user"  class="modal fade show"  aria-modal="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Reject Ticket</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="{{route('update-tiket')}}" method="post" name='reject'>
                    @csrf
                    <input type="hidden" id="reject-ticketno" name="ticketno"/>
                    <input type="hidden" id="reject-userid" name="userid"/>
                    <input type="hidden" id="reject-assignto" name="assignto"/>
                    <input type="hidden" id="reject-approvedby1" name="approvedby1"/>
                    <input type="hidden" id="reject-approveby_it" name="approveby_it"/>
                    <input type="hidden" id="reject-approveby_1_date" name="approveby_1_date"/>
                    <input type="hidden" id="reject-approveby_it_date" name="approveby_it_date"/>
                    <div class="modal-body">
                        <p>Are You Sure ? <span class="text-bold"></span></p>
                        <div class="form-group">
                            <div class="input-group value">
                                <input id="rejectedby" name="rejectedby" class="form-control input--style-6" type="hidden" value="{{ session('userid') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group value">
                                <input id="statusid" name="statusid" class="form-control input--style-6" type="hidden" value="SD005">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Yes</button>
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
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
<script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('image-upload/image-uploader.js') }}"></script>
@if(session('file_url'))
    <script>
        $(document).ready(function(){
            window.open("{{session('file_url')}}"); // will open new tab on document ready
        });
    </script>
@endif
<script>
    $('.nav-link.active').removeClass('active');
    $('#m-tiket').addClass('active');
    $('#m-tiket').parent().parent().parent().addClass('menu-is-opening menu-open');
</script>
<script>
    $(function () {    
        var today = new Date();
        var day = today.getDate() + "";
        var month = (today.getMonth() + 1) + "";
        var year = today.getFullYear() + "";
        var hour = today.getHours() + "";
        var minutes = today.getMinutes() + "";
        var seconds = today.getSeconds() + "";

        day = day;
        month = month;
        year = year;
        hour = hour;
        minutes = minutes;
        seconds = seconds;

        var date_range = year + "-" + month + "-" + day;
        var $btn_submit = $("button#btn-sumbit-ticket");

        //Initialize Select2 Elements
        $('.select2').select2()
        $('.datepicker').daterangepicker();

        $('#save-btn').on('click', function () {
            // console.log("clicked");
            var $inputs = $('modal-add-ticket form[name="ticket"] :input');
            $inputs.each(function () {
                if ($(this).attr('type') !== "button" && $(this).attr('type') !== undefined) {
                    if ($(this).val() === "") {
                        if ($(this).attr('name') === "category") {
                            $(this).parent().parent().parent().append(
                                '<span class="text-danger">Invalid input</span>');
                        } else {
                            $(this).parent().append(
                                '<span class="text-danger">Invalid input</span>');
                        }
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                        if ($(this).attr('name') === "subject") {
                            $(this).parent().parent().parent().children("span").remove();
                        } else {
                            $(this).parent().children("span").remove();
                        }
                    }
                }
            });
            var $form_valid = $('form[name="ticket"] :input.is-invalid');
            if ($form_valid.length === 0) {
                $('form[name="ticket"]').submit();
            }
        });

        $(document).on('click', '.view', function() {
            var user_id = $(this).attr('data-id');
            var ticketno = $(this).attr('data-ticket');
            var requestor = $(this).attr('data-requestor');
            var category = $(this).attr('data-category');
            var status = $(this).attr('data-status');
            var statusid  = $(this).attr('data-statusid');
            var priority  = $(this).attr('data-priority');
            var subject  = $(this).attr('data-subject');
            var detail  = $(this).attr('data-detail');
            var assign  = $(this).attr('data-assignto');
            var statusid  = $(this).attr('data-statusid');
            var roleid  = $(this).attr('data-roleid');
            var created  = $(this).attr('data-created');
            var approve  = $(this).attr('data-approve');
            var upload  = $(this).attr('data-upload');
            var $modal = $('#modal-view-user');
            var $form = $modal.find('form[name="view-user"]');
            $form.find('input[name="id"]').val(user_id);
            $form.find('input[name="ticketno"]').val(ticketno);
            $form.find('input[name="requestor"]').val(requestor);
            $form.find('input[name="category"]').val(category);
            $form.find('input[name="priority"]').val(priority);
            $form.find('input[name="subject"]').val(subject);
            $form.find('input[name="detail"]').val(detail);
            $form.find('input[name="assignto"]').val(assign);
            $form.find('input[name="statusid"]').val(statusid);
            $form.find('input[name="roleid"]').val(roleid);
            $form.find('input[name="status"]').val(status);
            $form.find('input[name="created"]').val(created);
            $form.find('input[name="approve"]').val(approve);
            $form.find('input[name="upload"]').val(upload);
            $form.find('input[name="comment"]').val();
            $modal.modal('show');
        });

        $('#modal-view-user form[name="view-user"] button#update-btn').on('click',function() {
            alert("test");
            var $inputs = $('#modal-view-user form[name="view-user"] :input');
            var $form_valid = $('#modal-view-user form[name="view-user"] :input.is-invalid');
            if ($form_valid.length === 0) {
                $('#modal-view-user form[name="view-user"]').submit();
            }
        });

        $(document).on('click', '.update', function () {
            $('#update-ticketno').val($(this).attr("data-ticketno"));
            $('#update-userid').val($(this).attr("data-userid"));
            $('#update-assignto').val($(this).attr("data-assignto"));
            $('#update-approvedby1').val($(this).attr("data-approvedby1"));
            $('#update-approvedbyit').val($(this).attr("data-approvedbyit"));
            $('#update-rejectedby').val($(this).attr("data-rejectedby"));
            $('#update-statusid').val($(this).attr("data-statusid"));
            $('#update-approvedby1_date').val($(this).attr("data-approvedby1_date"));
            $('#update-approvedbyit_date').val($(this).attr("data-approvedbyit_date"));
            $('#modal-update-user').modal('show');
        })

        $(document).on('click', '.reject', function () {
            // alert('bisa');
            $('#reject-ticketno').val($(this).attr("data-ticketno"));
            $('#reject-userid').val($(this).attr("data-userid"));
            $('#reject-assignto').val($(this).attr("data-assignto"));
            $('#reject-approvedby1').val($(this).attr("data-approvedby1"));
            $('#reject-approvedbyit').val($(this).attr("data-approvedbyit"));
            $('#reject-rejectedby').val($(this).attr("data-rejectedby"));
            $('#reject-statusid').val($(this).attr("data-statusid"));
            $('#reject-approvedby1_date').val($(this).attr("data-approvedby1_date"));
            $('#reject-approvedbyit_date').val($(this).attr("data-approvedbyit_date"));
            $('#modal-reject-user').modal('show');
        })

        $(document).on('click', '.ticket', function submit() {
            var daterange = $('input[name="data_date_range"]').val();
            var requestor = $('select[name="requestor"]  option:selected').val();
            var assignto = $('select[name="assignto"] option:selected').val();
            var status = $('select[name="status"] option:selected').val();
            var ticketno = $('select[name="ticketno"] option:selected').val();
            console.log(daterange);
            console.log(status);
            console.log(ticketno);
            console.log(requestor);
            console.log(assignto);
           
            $('#tiket_list').DataTable().clear().destroy();
            var $dataticket = $('#tiket_list').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                responsive: true,
                searching: true,
                ajax: {
                    url: "{{ route('filter-tiket') }}",
                    "data": function (d) {
                        d.daterange = $('input[name="data_date_range"]').val();
                        d.requestor = $('select[name="requestor"] option:selected').val();
                        d.assignto = $('select[name="assignto"] option:selected').val();
                        d.status = $('select[name="status"] option:selected').val();
                        d.ticketno = $('select[name="ticketno"] option:selected').val();
                    },
                    "dataSrc": function (settings) {
                        $btn_submit.text("Submit");
                        $btn_submit.prop('disabled', false);
                        return settings.data;
                    },
                },
                order: [[ 0, "desc" ]],
                columns: [
                    {
                        data: 'ticketno',
                        name: 'ticketno'
                    },
                    {
                        data: 'category',
                        render: function(data) {
                            if(data == 'INCIDENT'){
                                statusText = `<button class="btn btn-dark">INCIDENT</span>`;
                            } else if (data == 'CHANGE REQUEST'){
                                statusText = `<button class="btn btn-warning">CHANGE REQUEST</span>`;
                            } else {
                                statusText = `<button class="btn btn-info">NEW USER</span>`;
                            }
                            return statusText;
                        }
                    },
                    {
                        data: 'priority',
                        render: function (data){
                            if(data == "HIGH"){
                                statusText = `<button class="btn btn-danger">HIGH</button>`;
                            } else if(data == "MEDIUM"){
                                statusText = `<button class="btn btn-info">MEDIUM</button>`;
                            } else {
                                statusText = `<button class="btn btn-primary">LOW</button>`;
                            }
                            return statusText;
                        }
                    },
                    {
                        data: 'requestor',
                        name: 'requestor'
                    },
                    {
                        data: 'assigned_to',
                        name: 'assigned_to'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'createdon',
                        render: function(data) {
                            var today = new Date(data);
                            var day = today.getDate() + "";
                            var month = (today.getMonth() + 1) + "";
                            var year = today.getFullYear() + "";
                            var hour = (today.getHours() < 10 ? '0' : '') + today.getHours();
                            var minutes = (today.getMinutes() < 10 ? '0' : '' ) + today.getMinutes();
                            var seconds = today.getSeconds() + "";

                            day = day;
                            month = month;
                            year = year;
                            hour = hour;
                            minutes = minutes;
                            seconds = seconds;
                            // console.log(day + "/" + month + "/" + year + " " + hour + ":" + minutes + ":" + seconds);
                            var date = day + "/" + month + "/" + year;
                            return date;   
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                    },
                ],
                oLanguage: {
                    "sLengthMenu": "Tampilkan _MENU_ data",
                    "sProcessing": "Memproses...",
                    "sSearch": "Cari data:",
                    "sInfo": "Menampilkan _START_ - _END_ dari _TOTAL_ data" 	
                },
                drawCallback: function() {
                    $btn_submit.text("Sumbit");
                    $btn_submit.prop('disabled', false);
                }
            });
        });

        var table = $('#tiket_list').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            searching: true,
            ajax: "{{ route('get-tiket') }}",
            order: [[ 0, "desc" ]],
            columns: [
                {
                    data: 'ticketno',
                    name: 'ticketno'
                },
                {
                    data: 'category',
                    render: function(data) {
                        if(data == 'INCIDENT'){
                            statusText = `<button class="btn btn-dark">INCIDENT</span>`;
                        } else if (data == 'CHANGE REQUEST'){
                            statusText = `<button class="btn btn-warning">CHANGE REQUEST</span>`;
                        } else {
                            statusText = `<button class="btn btn-info">NEW USER</span>`;
                        }
                        return statusText;
                    }
                },
                {
                    data: 'priority',
                    render: function (data){
                        if(data == "HIGH"){
                            statusText = `<button class="btn btn-danger">HIGH</button>`;
                        } else if(data == "MEDIUM"){
                            statusText = `<button class="btn btn-info">MEDIUM</button>`;
                        } else {
                            statusText = `<button class="btn btn-primary">LOW</button>`;
                        }
                        return statusText;
                    }
                },
                {
                    data: 'requestor',
                    name: 'requestor'
                },
                {
                    data: 'assigned_to',
                    name: 'assigned_to'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'createdon',
                    render: function(data) {
                        var today = new Date(data);
                        var day = today.getDate() + "";
                        var month = (today.getMonth() + 1) + "";
                        var year = today.getFullYear() + "";
                        var hour = (today.getHours() < 10 ? '0' : '') + today.getHours();
                        var minutes = (today.getMinutes() < 10 ? '0' : '' ) + today.getMinutes();
                        var seconds = today.getSeconds() + "";

                        day = day;
                        month = month;
                        year = year;
                        hour = hour;
                        minutes = minutes;
                        seconds = seconds;
                        // console.log(day + "/" + month + "/" + year + " " + hour + ":" + minutes + ":" + seconds);
                        var date = day + "/" + month + "/" + year;
                        return date;   
                    }
                },
                {
                    data: 'action',
                    name: 'action',
                },
            ],
            oLanguage: {
				"sLengthMenu": "Tampilkan _MENU_ data",
				"sProcessing": "Memproses...",
				"sSearch": "Cari data:",
				"sInfo": "Menampilkan _START_ - _END_ dari _TOTAL_ data" 	
			},
        });
    });

    var selectOption = parseInt(document.getElementById("status").value);

    $(document).on('keypress', '.select2-search__field', function () {
    $(this).val($(this).val().replace(/[^\d].+/, ""));
        if ((event.which < 48 || event.which > 57)) {
          event.preventDefault();
        }
    });
</script>

<script>
    window.setTimeout(function() {
    $(".alert-message").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); 
    });
}, 5000);
</script>
<script>
    $('.toast').toast('show');
</script>
@endsection
