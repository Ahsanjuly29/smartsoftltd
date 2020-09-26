<?php

namespace App\Http\Controllers\Admin;

use App\StudentProfile;
use App\SiteConfig;
use App\Transactions\StudentChart;
use App\Transactions\Transaction;
use App\Configuration;
use App\Account;
use App\StudentPayment;
use App\StudentBatch;
use App\StudentSession;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class StudentPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
         $items = StudentPayment::all();
         $siteconf = SiteConfig::first();
         $accounts = Account::all();
        
        // $transactionData = Transaction::where('transactionable_type','App\Transactions\StudentChart')->get();

        // foreach ($transactionData as $value) {
        //   $data = Transaction::where('id',$value->transactionable_id)->first();
        //   dd($data);
        // }
        // foreach($items as $item){
        //    // $item['account_type'] = StudentPayment::findName($item['account_type']);
        //    $item['student_id'] = StudentPayment::findStudentName($item['student_id']);
        // }
        return view('admin.studentPayment.index', compact('items','siteconf','accounts'));
    }
 
    public function create(){

         $siteconf = SiteConfig::first();
         $studentProfile = StudentProfile::all();
         $configuration = Configuration::all();
         $accounts = Account::where('type','=','academic')->get();
         
        return view('admin.studentPayment.create',compact('studentProfile','configuration','accounts','siteconf'));
    } 

    public function ajaxGetBatch(Request $request){
        $id = $request->id;
        $student = StudentProfile::
                   where('id', $id)
                   ->first();
        $batch   = StudentBatch::
                   where('id',$student->batch) 
                   ->get();
         $session = StudentSession::
                    where('id',$student->session)
                    ->get();
        return response()->json([
            'batch' => $batch,
            'session' => $session
        ]);
    } 
 
    public function store(Request $request){

          //  dd($request->all());
          $data = $request->validate([
            'student_id'  => 'required',
            'date'        => 'required',
            'amount'      => 'required',
            'discount'      => 'nullable',
        ]);

        $data['created_at'] = now();
        $data['updated_at'] = now();
          // DB::transaction(function()use($request){
             
            StudentPayment::insert($data);
            $invoice = StudentPayment::latest()->first();

            // StudentCharts
              foreach ($request->account_type as $key => $account_type){

                // dd($invoice->toarray());
                 $chart = $invoice->charts()->create([
                    'chart_of_account_id' => $account_type,
                    'description' => $request->description[$key]?$request->description[$key]:'N/A',
                    'amount' => $request->payable[$key]
                 ]); 

                 if($request->paid[$key]){
                  $chart->payment()->create([  
                    'amount' => $request->paid[$key]
                  ]); 
                 }
              }   
        return redirect('student-income/'.$invoice->id);
    }
    
    public function student_income($id){ 

            $basicData = SiteConfig::first(); 
            $data = StudentPayment::with('charts')->where('id',$id)->first();
            $transaction = DB::table('transactions')
                           ->where('transactionable_type','=','App\StudentChart')
                           ->where('transactionable_id','=',$id)
                           ->get();
              // dd($transaction);
              return view('admin.studentPayment.student_payment_view',compact('data','basicData','transaction'));
          }
 
    public function destroy(StudentPayment $studentPayment){
        $studentPayment->delete();
        return redirect()->route('student-payment.index')->with('message','Payment Successfully Deleted');
    }

     //------------------Search Medicine------------------//
    public function search_student(Request $request)
     {
       $data = StudentProfile::selectRaw('name as name, id')  
                  ->where('name','LIKE', '%'.$request->name.'%')
                  ->where('status',1)
                  ->pluck('name', 'id'); 
          return response()->json($data);
    }

     //------------------Search Medicine------------------//
    public function student_search($id){
       $data = StudentProfile:: 
                  where('id',$id)
                  ->select('name','id')
                  ->first(); 
          return response()->json($data);
    }
}
