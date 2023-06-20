@extends('parent.master')
@section('extend-css')
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables/dataTables.checkboxes.css') }}">
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
                    <h1>Laporan Absensi</h1>
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
                            <div class="card-header">
                                <div class="row align-items-end">
                                    <div class="col-md-2">
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
                                            <button id="absen" name="absen" class="absen btn-submit btn btn-success" >Search</button>
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-2">
                                        <div class="form-group">
                                            <button class="btn btn-success" ><i class="fas fa-file-excel"></i> Export</button>
                                        </div>
                                    </div> -->
                                </div>
                            </div>
                            <form name="formData" action="">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <table id="dataabsen" class="table table-bordered table-hover display nowrap" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Name</th>
                                                        <th>Date</th>
                                                        <th>Remark</th>
                                                        <th>Clock 1</th>
                                                        <th>Clock 2</th>
                                                        <th>Clock 3</th>
                                                        <th>Clock 4</th>
                                                        <th>Clock 5</th>
                                                        <th>Clock 6</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <!-- <div class="card-footer">
                            Indo Sukses Logistic
                        </div> -->
                    <!-- /.card-footer-->
                </div>
                <!-- /.card -->
            </div>
        </div>
    </section>
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
<script>
    $('.nav-link.active').removeClass('active');
    $('#m-Attendance').addClass('active');
    $('#m-Attendance').parent().parent().parent().addClass('menu-is-opening menu-open');
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

        var date_range = day + "/" + month + "/" + year;
        console.log(date_range);    
        var $btn_submit = $("button#btn-sumbit-absen");

        //Initialize Select2 Elements
        $('.select2').select2()
        $('.datepicker').daterangepicker();

        $(document).on('click', '.absen', function submit() {
            daterange = $('input[name="data_date_range"]').val();
           
            $('#dataabsen').DataTable().clear().destroy();
            var $dataabsen = $('#dataabsen').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                responsive: true,
                searching: false,
                dom: 'Blfrtip',
                buttons: [
                    'csv'
                ],
                ajax: {
                    url: '{{ route("get-absensi") }}',
                    "data": function (d) {
                        d.daterange = $('input[name="data_date_range"]').val();
                    },
                    "dataSrc": function (settings) {
                        $btn_submit.text("Submit");
                        $btn_submit.prop('disabled', false);
                        return settings.data;
                    },
                },
                columns: [
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'tgl',
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
                            var date = day + "/" + month + "/" + year + " " + hour + ":" + minutes;
                            return date;   
                        }
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'j1',
                        name: 'j1'
                    },
                    {
                        data: 'j2',
                        name: 'j2'
                    },
                    {
                        data: 'j3',
                        name: 'j3'
                    },
                    {
                        data: 'j4',
                        name: 'j4'
                    },
                    {
                        data: 'j5',
                        name: 'j5'
                    },
                    {
                        data: 'j6',
                        name: 'j6'
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

        var $table = $('#dataabsen').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            searching: true,
            dom: 'Blfrtip',
            buttons: [
                'csv'
            ],
            ajax: {
                "url": '{{ route("get-absensi") }}',
                "data": function (d) {
                    d.daterange = $('input[name="data_date_range"]').val();
                },
                "dataSrc": function (settings) {
                    $btn_submit.text("Submit");
                    $btn_submit.prop('disabled', false);
                    return settings.data;
                },
            },
            columns: [
                {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'nama',
                    name: 'nama'
                },
                {
                    data: 'tgl',
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
                        var date = day + "/" + month + "/" + year + " " + hour + ":" + minutes;
                        return date;   
                    }
                },
                {
                    data: 'keterangan',
                    name: 'keterangan'
                },
                {
                    data: 'j1',
                    name: 'j1'
                },
                {
                    data: 'j2',
                    name: 'j2'
                },
                {
                    data: 'j3',
                    name: 'j3'
                },
                {
                    data: 'j4',
                    name: 'j4'
                },
                {
                    data: 'j5',
                    name: 'j5'
                },
                {
                    data: 'j6',
                    name: 'j6'
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

    // $(document).on('click','.absen', function submit() {
    //     var  daterange = $('input[name="data_date_range"]').val();
      
    //     $.ajax({
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         type: "GET",
    //         url: "/absensi/get",
    //         data: {
    //             'daterange' : daterange,
    //         },
    //         success: function(resp) {
    //             console.log(resp);
    //         },
    //     });
    // });
    
 
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
