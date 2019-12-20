<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Model\BlogCategory;
use App\Model\BlogPost;
use App\Model\Promotions;
use Illuminate\Http\Request;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Input;
use File;
use App\Http\Controllers\Controller;

class PromotionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $cat_id = 1; //Promotions

    public function index()
    {
        $data['page_title'] = "All Promotions";
        $data['posts'] = BlogPost::where('cat_id', $this->cat_id)->latest()->paginate(15);
        return view('backend.admin.promotions.index', $data);
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $data['page_title'] = 'Add Promotions';
        $data['category'] = BlogCategory::where('id', $this->cat_id)->whereStatus(1)->get();
        return view('backend.admin.promotions.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'image' => 'required | mimes:jpeg,jpg,png | max:1000'
        ],
            [
                'title.required' => 'Post Title Must not be empty',
                'cat_id.required' => 'Category Must be selected',
            ]
        );

        $in = Input::except('_token');
        if($request->hasFile('image')){
            $image = $request->file('image');
            $filename = 'post_'.time().'.jpg';
            $location = 'assets/backend/image/blog/post/' . $filename;
            Image::make($image)->resize(700,350)->save($location);
            $in['image'] = $filename;
        }

        if($request->hasFile('image')){
            $image = $request->file('image');
            $filename = 'post_thumb'.time().'.jpg';
            $location = 'assets/backend/image/blog/post/' . $filename;
            Image::make($image)->resize(350,213)->save($location);
            $in['thumb'] = $filename;
        }


        $in['status'] =  $request->status == 'on' ? '1' : '0';
        $in['cat_id'] =  $this->cat_id;
        $res = BlogPost::create($in);
        if ($res) {
            $notification = array('message' => 'Updated Successfully!', 'alert-type' => 'success');
            return back()->with($notification);
        } else {
            $notification = array('message' => 'Problem With Updating Post', 'alert-type' => 'error');
            return back()->with($notification);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Promotions  $promotions
     * @return \Illuminate\Http\Response
     */
    public function show(Promotions $promotions)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Promotions  $promotions
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['page_title'] = 'Edit Promotions';
        $data['post'] = BlogPost::findOrFail($id);
        $data['category'] = BlogCategory::where('id', $this->cat_id)->whereStatus(1)->get();
        return view('backend.admin.promotions.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Promotions  $promotions
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Promotions $promotions)
    {
        //
    }

    public function updatePost(Request $request)
    {

        $data = BlogPost::find($request->id);
        $request->validate([
            'title' => 'required',
            'details' => 'required',
            'image' => 'nullable | mimes:jpeg,jpg,png | max:1000'
        ],
            [
                'title.required' => 'Post Title Must not be empty',
                'cat_id.required' => 'Category Must be selected',
                'details.required' => 'Post Details  must not be empty',
            ]
        );


        $in = Input::except('_token');
        if($request->hasFile('image')){
            $image = $request->file('image');
            $filename = 'post_'.time().'.jpg';
            $location = 'assets/backend/image/blog/post/' . $filename;
            Image::make($image)->resize(700,350)->save($location);
            $path = 'assets/backend/image/blog/post/';
            @unlink($path.$data->image);
            $in['image'] = $filename;
        }


        if($request->hasFile('image')){
            $image = $request->file('image');
            $filename = 'post_thumb'.time().'.jpg';
            $location = 'assets/backend/image/blog/post/' . $filename;
            Image::make($image)->resize(350,213)->save($location);

            $path = 'assets/backend/image/blog/post/';
            @unlink($path.$data->thumb);
            $in['thumb'] = $filename;
        }
        $in['status'] =  $request->status == 'on' ? '1' : '0';
        $in['cat_id'] =  $this->cat_id;
        $res = $data->fill($in)->save();

        if ($res) {
            $notification = array('message' => 'Updated Successfully!', 'alert-type' => 'success');
            return back()->with($notification);
        } else {
            $notification = array('message' => 'Problem With Updating Post!', 'alert-type' => 'error');
            return back()->with($notification);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Promotions  $promotions
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);
        $data = BlogPost::findOrFail($request->id);
        $path = 'assets/backend/image/blog/post/';
        // @unlink($path.$data->image);
        // @unlink($path.$data->thumb);
        // $res =  $data->delete();
        $res =  $data->update(['status' => 0]);

        if ($res) {
            $notification = array('message' => 'Post Delete Successfully!', 'alert-type' => 'success');
            return back()->with($notification);
        } else {
            $notification = array('message' => 'Problem With Deleting Post!', 'alert-type' => 'error');
            return back()->with($notification);
        }
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);
        $data = BlogPost::findOrFail($request->id);
        $path = 'assets/backend/image/blog/post/';
        @unlink($path.$data->image);
        @unlink($path.$data->thumb);
        $res =  $data->delete();

        if ($res) {
            $notification = array('message' => 'Post Delete Successfully!', 'alert-type' => 'success');
            return back()->with($notification);
        } else {
            $notification = array('message' => 'Problem With Deleting Post!', 'alert-type' => 'error');
            return back()->with($notification);
        }
    }
}
