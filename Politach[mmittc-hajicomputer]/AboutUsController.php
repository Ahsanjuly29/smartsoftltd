<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\AboutUs;
use App\SiteConfig;
// use Carbon\Carbon;
use Illuminate\Support\Str;

use File;
// use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;

class AboutUsController extends Controller
{
   /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //    $siteconf = SiteConfig::first();
    //     $items = AboutUs::all();
    //     return view('admin.aboutUs.index',compact('items','siteconf'));
    // }
      public function showAboutForm(){
        $siteconf = SiteConfig::first();
        $items = AboutUs::first();
         //dd($items);
        return view('admin.aboutUs',compact('items','siteconf'));
    }
    public function aboutUsPost(Request $request){
         
          $this->validate($request,[
            'title' => 'required|max:190',
            'about' => 'required',
//             'image' => 'nullable|mimes:jpg,jpeg,png,PNG|dimensions:max_width=1220,max_height=150',

             'image' => 'nullable|mimes:jpg,jpeg,png,PNG',
            'link' => 'required|max:70',
            
             'boardimage' => 'nullable|mimes:jpg,jpeg,png,PNG',
            'boardlink' => 'required|max:70',

             'image2' => 'nullable|mimes:jpg,jpeg,png,PNG',
            'link2' => 'required|max:70',

         ]);
      //  dd($request->all());
        //  $items = new AboutUs();
          // $items = AboutUs::firstOrCreate();

          $items = AboutUs::first();

           if ($request->hasFile('image')) {
            //delete previous image
            $image_path = "uploads/aboutus/".$items->image;  // Value is not URL but directory file path
            if( File::exists($image_path)) {
                // dd($image_path);
                File::delete($image_path);
            }

            $imageName = "aboutUs".'.'.$request->image->getClientOriginalExtension();
            // dd($imageName);
            $request->image->move(public_path('uploads/aboutus/'), $imageName);
            $items->image = $imageName;
          }

          if ($request->hasFile('boardimage')) {
            //delete previous image
            $image_path = "uploads/aboutus/".$items->boardimage;  // Value is not URL but directory file path
            if(File::exists($image_path)) {
                // dd($image_path);
                File::delete($image_path);
          }

//            $imageName = $items->boardimage.'.'.$request->boardimage->getClientOriginalExtension();
            $imageName = $items->boardimage;
            // dd($imageName);
            $request->boardimage->move(public_path('uploads/aboutus/'), $imageName);
            $items->boardimage = $imageName;
        }

           if ($request->hasFile('image2')) {
            //delete previous image
            $image_path = "uploads/aboutus/".$items->image2;  // Value is not URL but directory file path
            if(File::exists($image_path)) {
                // dd($image_path);
                File::delete($image_path);
            }

            $imageName = $items->image2;
//            $imageName = $items->image2.'.'.$request->image2->getClientOriginalExtension();
            // dd($imageName);
            $request->image2->move(public_path('uploads/aboutus/'), $imageName);
            $items->image2 = $imageName;
        }


          // if ($request->file('image')) {
          //         $upload_image_name = "";

          //         $image = $request->file('image');
          //         $slug = Str::slug($request->title);


          //         if (isset($image)) {
          //         $imageName = $slug.'-'.uniqid().'.'.$image->getClientOriginalExtension();
          //         $year = date('Y');
          //         $month = date('m');
          //         // $directory = './uploads/'.'aboutus/'.$year.'/'.$month.'/';
          //         $directory = "uploads/aboutus";

          //         $image->move($directory, $imageName);
                   
          //         $upload_image_name = $imageName;
          //         }
          //         $items->image = $upload_image_name; 
          //    }
        
                  $items->title = $request->title;
                  $items->about = $request->about;
                  $items->link = $request->link;
                  $items->boardlink = $request->boardlink;
                  $items->link2 = $request->link2;

                  // $items->image = $imageName;
                  
                  $items->save();
      // dd($request->all());
      return redirect()->route('aboutUs')->with('message','About Us Successfully Added');
     
    }

    
}
