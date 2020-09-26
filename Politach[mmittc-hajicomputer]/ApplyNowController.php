<?php

namespace App\Http\Controllers\User;

use App\SiteConfig;
use App\ApplyNow;
use App\Course;
use App\Configuration;
use Carbon\Carbon;
use File;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApplyNowController extends Controller
{
    public function index(){
        $siteConfig = SiteConfig::first();
        $course = Course::get();
        $configReligion = Configuration::where('religion',1)->pluck('data', 'id');
        $configBloodGrp = Configuration::where('bloodGroup',1)->pluck('data', 'id');
        $configGender = Configuration::where('gender',1)->pluck('data', 'id');

	        // foreach($configuration as $item){
	        // $item['batch'] = StudentProfile::findName($item['batch']);
	        // $item['bloodGroup'] = StudentProfile::findName($item['bloodGroup']);
	        // } 
         // @dd($courses);
    	return view('user.applyNow.index',compact('siteConfig','course','configReligion','configBloodGrp','configGender'));
    }

    public function applyStudentform(Request $request){
//          dd($request->all());
    
          $this->validate($request,[
           'name'            => 'required',
           'fatherName'      => 'nullable',
           'motherName'      => 'nullable',
           'phone'           => 'required|regex:/(01)[0-9]{9}/|max:11',
           'email'           => 'nullable',
           'religion'           => 'required',
           'course'           => 'required',
           'guardianPhone'   => 'nullable',
           'religion'        => 'nullable',
           'dob'             => 'nullable|max:40',
           'bloodGroup'      => 'nullable|max:250',
           'gender'          => 'nullable',
           'lastEduProfile'  => 'nullable',
           'image'           => 'required|mimes:jpeg,jpg,png|nullable',
           'presentAddress'  => 'nullable',
           'permanentAddress'=> 'nullable',
       ]);
          $image = $request->file('image'); 
          $slug = str_slug($request->name);
 
        if($request->hasFile('image')) {
            $std_img = $request->image;
                $extension = $std_img->getClientOriginalExtension();
                $remove_space = str_replace(" ","",$request->name);
                $trim = $remove_space.time().str_random(5);
                $fileName = $trim.".".$extension;
                $folderpath = 'uploads/studentprofile/';
                $image_url = $folderpath.$fileName;
                $std_img->move($folderpath , $fileName);
                $filename = $image_url;
            }
        else { 
            $filename =  ('no');
        }
         $item = new ApplyNow();
         $item->name = $request->name;
         $item->fatherName = $request->fatherName;
         $item->motherName = $request->motherName;
         $item->course = $request->course;
         $item->phone = $request->phone;
         $item->email = $request->email;
         $item->guardianPhone = $request->guardianPhone;
         $item->religion = $request->religion;
         $item->dob = $request->dob;
         $item->bloodGroup = $request->bloodGroup;
         $item->gender = $request->gender;
         $item->lastEduProfile = $request->lastEduProfile;
         $item->image = $filename;
         $item->presentAddress = $request->presentAddress;
         $item->permanentAddress = $request->permanentAddress;

         $item->save();
         return redirect()->route('user.applyNow')->with('successMsg','Your information send Successfully');


    }
}
