@extends('admin.layouts.master')
@section('title','Students Profile Form')
@section('page-header')
    <i class="fa fa-plus-circle"></i> Students due list
@stop
@section('css')
    <link rel="stylesheet" href="{{ asset('admin/assets/css/jquery.gritter.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/css/jquery.gritter.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/css/jquery-ui.custom.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/css/chosen.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/css/bootstrap-datepicker3.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/css/bootstrap-timepicker.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/css/daterangepicker.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/css/bootstrap-datetimepicker.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/css/bootstrap-colorpicker.min.css') }}" />
    @livewireAssets
@stop
@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="clearfix">
                <div class="pull-right tableTools-container"></div>
            </div>
            <div class="table-header">
                Search Students using by Name,Id,Batch,
            </div>
            <div class="table-header">
                @php
                    $sessions = App\StudentSession::all();
                    $batches = App\StudentBatch::all();
                    $courses = App\Course::all();
                @endphp
                <div style=" padding: 10PX; display: flow-root;">
                    <form action="#" method="GET">
                    
                        {{--  @dd($courses->toArray());  --}}

                        <div class="col-md-3">
                            <select class="form-control" name="session" id="">
                                <option value="null"> Selecet a Session name / Number</option>
                                @foreach($sessions as $session)
                                    <option value="{{ $session->id }}">
                                        {{ $session->session_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" name="batch" id="">
                                <option value="null"> Selecet a Batch name / Number</option>
                                @foreach($batches as $batch)
                                    <option value="{{ $batch->id }}">
                                       {{ $batch->batch_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" name="course" id="">
                                <option value="null"> Selecet a Course</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}">
                                        {{ $course->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="submit" class="btn btn-sm btn-success" 
                                   style="width:100%;" value="Search" >
                        </div>
                        {{--  <div class="col-md-2">                </div>  --}}
                    </form>
                </div>             
            </div>

            <div>
                <table id="dynamic-table" class="table table-striped item-table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Session</th>
                            <th>Batch Name</th>
                            <th>Student ID</th>
                            <th>Payment Type</th>

                            <th>Payable Amount</th>                            
                            <th>Paid Amount</th>
                            <th>Discount</th>
                            <th>Due Amount</th>

                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i=1;
                            $j=1;
                        @endphp
                        @foreach($student_charts as $data)
                            <?php
                                $paid_amount = 0;
                                    $transactions = \App\Transactions\Transaction::
                                    where('transactionable_type','=','App\Transactions\StudentChart')
                                    ->where('transactionable_id','=',$data->id)
                                    ->get();
                                $check_amount = 0;
                                    $chek_transactions = \App\Transactions\Transaction::
                                    where('transactionable_type','=','App\Transactions\StudentChart')
                                    ->where('transactionable_id','=',$data->id)
                                    ->get();
                                    foreach ($chek_transactions as $check){
                                        $check_amount = $check_amount+$check->amount;
                                    }
                        // compare transaction amount with paid amount and show here
                        if($check_amount < $data->amount){ ?>
                            <tr>
                                <?php
                                    $payment_info = \App\StudentPayment::where('student_id','=',$data->studentPayment->student_id)->firstOrFail();
                                    $acc_name = \App\Account::where('id','=',$data->chart_of_account_id)->firstOrFail();
                                    
                                    $st_info  = \App\StudentProfile::where('id','=',$payment_info->student_id)->firstOrFail();
                                ?>
                                {{--  @dd($st_info->toArray());  --}}
                                <td>{{$st_info->name}}</td>
                                @php
                                    $sessionName = App\StudentSession::where('id','=',$st_info->session)->firstOrFail();
                                @endphp
                                <td>{{ $sessionName->session_name }}</td>
                                @php
                                    $batchName = App\StudentBatch::where('id','=',$st_info->batch)->firstOrFail();
                                @endphp
                                <td>{{ $batchName->batch_name }}</td>
                                <td>{{!empty($st_info->studentId)?$st_info->studentId:''}}</td>
                                <td>{{$acc_name->name_of_account}}</td>
                                <td>{{$data->amount}}</td>

                                @foreach($transactions as $transac)
                                    @php
                                        $paid_amount = $paid_amount+$transac->amount;
                                    @endphp
                                @endforeach
                                <td>
                                    {{$paid_amount}}
                                </td>
                                @php
                                    $discount=App\studentPayment::where('student_id','=',$data->studentPayment->id)->first('discount')['discount'];
                                @endphp
                                <td>{{number_format((float)$discount, 2, '.', '') ?? ''}}</td>

                                <td>
                                    {{$data->amount - $paid_amount - $discount}}
                                </td>
                                
                                <td>
                                    <div class="hidden-sm hidden-xs action-buttons">
                                        <!-- Button trigger modal -->
                                        <button style="height: 30px;width:100%" type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#exampleModal{{$i++}}">
                                            Pay Due
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <div class="modal fade" id="exampleModal{{$j++}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">
                                                Pay Due Amount

                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </h5>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{url('due-payment')}}" method="post">
                                                @csrf
                                                <input type="hidden" name="chart_id" value="{{$data->id}}">
                                                <div class="form-group">
                                                    <label for="student_name" class="col-form-label">Student Name:</label>
                                                    <input type="text" class="form-control" id="student_name" value="{{$st_info->name}}" readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label for="student_id" class="col-form-label">Student Id:</label>
                                                    <input type="text" class="form-control" id="student_id" value="{{$st_info->id}}" readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label for="payment_info" class="col-form-label">Payment Info:</label>
                                                    <input type="text" class="form-control" id="payment_info" value="{{$acc_name->name_of_account}}" readonly>
                                                </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="payment_amount" class="col-form-label">Due Amount:</label>
                                                        <input type="number" class="form-control due_amount" name="due_amount"
                                                                id="due_amount" value="{{$data->amount - $paid_amount - $discount}}" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="payment_amount" class="col-form-label">Pay Amount:</label>
                                                        <input type="number" class="form-control pay_amount" name="payment_amount" id="payment_amount" placeholder="Cash Amount" min="10" required>
                                                    </div>
                                                </div>
                                            </div>
                                                <div style="background-color: #FFF!important;" class="modal-footer">
                                                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
                                                    <input  type="submit" class="btn btn-sm btn-primary" value="Submit">
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                    <?php }else{ ?>

                                    <?php } ?>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
 
    </div>
@endsection
    @section('js')
        <script src="{{ asset('admin/assets/js/jquery-ui.min.js') }}"></script>
        <script src="{{ asset('admin/assets/js/jquery-ui.custom.min.js') }}"></script>
        <script src="{{ asset('admin/assets/js/jquery.ui.touch-punch.min.js') }}"></script>
        <script src="{{ asset('admin/assets/js/chosen.jquery.min.js') }}"></script>
        <script src="{{ asset('admin/assets/js/spinbox.min.js') }}"></script>
        <script src="{{ asset('admin/assets/js/bootstrap-datepicker.min.js') }}"></script>
        <script src="{{ asset('admin/assets/js/bootstrap-timepicker.min.js') }}"></script>
        <script src="{{ asset('admin/assets/js/moment.min.js') }}"></script>
        <script src="{{ asset('admin/assets/js/daterangepicker.min.js') }}"></script>
        <script src="{{ asset('admin/assets/js/bootstrap-datetimepicker.min.js') }}"></script>
        <script src="{{ asset('admin/assets/js/bootstrap-colorpicker.min.js') }}"></script>
        <script src="{{ asset('admin/assets/js/jquery.knob.min.js') }}"></script>
        <script src="{{ asset('admin/assets/js/autosize.min.js') }}"></script>
        <script src="{{ asset('admin/assets/js/jquery.inputlimiter.min.js') }}"></script>
        <script src="{{ asset('admin/assets/js/jquery.maskedinput.min.js') }}"></script>
        <script src="{{ asset('admin/assets/js/bootstrap-tag.min.js') }}"></script>
        <script src="{{ asset('admin/assets/js/ace-elements.min.js') }}"></script>
        <script src="{{ asset('admin/assets/js/ace.min.js') }}"></script>
        
        <!-- page specific plugin scripts -->
       
        <script src="{{ asset('admin/')}}/assets/js/jquery.dataTables.min.js"></script>
        <script src="{{ asset('admin/')}}/assets/js/jquery.dataTables.bootstrap.min.js"></script>
        <script src="{{ asset('admin/')}}/assets/js/dataTables.buttons.min.js"></script>
        <script src="{{ asset('admin/')}}/assets/js/buttons.flash.min.js"></script>
        <script src="{{ asset('admin/')}}/assets/js/buttons.html5.min.js"></script>
        <script src="{{ asset('admin/')}}/assets/js/buttons.print.min.js"></script>
        <script src="{{ asset('admin/')}}/assets/js/buttons.colVis.min.js"></script>
        <script src="{{ asset('admin/')}}/assets/js/dataTables.select.min.js"></script>

        <script>
                $(".pay_amount").keyup(function(){
                    var cashAmount = parseInt(Number($(this).val()));
                    var dueAmount = parseInt(Number(this.parentNode.parentNode.parentNode.querySelector('#due_amount').value));
                    if(cashAmount > dueAmount){
                        alert("Due Amount Is Too Large");
                        // location.reload();
                    }
                });
        </script>

    <script type="text/javascript">
        jQuery(function($) {
            $('#dynamic-table').DataTable({
                "ordering": false,
                // install laravel datatable this package
                // https://github.com/yajra/laravel-datatables
                // processing: true,
                // serverSide: true,
                {{--ajax: '{{ url('') }}',--}}
                // columns:[
                //     {"data":"first_name"},
                //     {"data":"last_name"},
                // ],
                "bPaginate": true,
            });

        });
        
        {{--  $(document).ready(function(){
            $('#dynamic-table_filter').find('input').val('#text')
            console.log('################################################################')
            console.log($('#dynamic-table_filter').html())
            var tbl = $('#dynamic-table_filte').DataTable({
                "oSearch": {"sSearch": "Initial search"}
            });
        });  --}}

    </script>

        <!-- Yandex.Metrika counter -->
        <script type="text/javascript">
            (function (d, w, c) {
                (w[c] = w[c] || []).push(function() {
                    try {
                        w.yaCounter25836836 = new Ya.Metrika({id:25836836,
                            webvisor:true,
                            clickmap:true,
                            trackLinks:true,
                            accurateTrackBounce:true});
                    } catch(e) { }
                });

                var n = d.getElementsByTagName("script")[0],
                    s = d.createElement("script"),
                    f = function () { n.parentNode.insertBefore(s, n); };
                s.type = "text/javascript";
                s.async = true;
                s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

                if (w.opera == "[object Opera]") {
                    d.addEventListener("DOMContentLoaded", f, false);
                } else { f(); }
            })(document, window, "yandex_metrika_callbacks");
        </script>

        @if(Session::has('success'))
            <script>
                Swal.fire({
                    title: 'Success!',
                    text: 'Due Payment Success',
                    imageUrl: '{{asset('admin/assets/images/taka1.png')}}',
                    imageWidth: 200,
                    imageHeight: 200,
                    imageAlt: 'Custom image',
                    timer:2000,
                })
            </script>
        @endif
        @if(Session::has('payment_failure'))
            <script>
                Swal.fire({
                    title: 'Error!',
                    text: 'Please Try Again',
                    imageUrl: '{{asset('admin/assets/images/payment_failure.png')}}',
                    imageWidth: 200,
                    imageHeight: 200,
                    imageAlt: 'Custom image',
                    timer:4000,
                })
            </script>
        @endif
@stop