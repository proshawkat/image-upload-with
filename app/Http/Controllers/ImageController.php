<?php

namespace App\Http\Controllers;

use App\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ImageController extends Controller
{
    public function store(Request $request){

        $request->validate([
            'title'     => 'required',
            'image'     => 'required|image|mimes:png|max:5048',
        ]);

        $images = new Image();

        $images->title   = $request->title;

        if ($request->has('image')) {
            $image      = $request->file('image');
            $fileName   = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/images', $fileName);
            $images->image                       = $fileName;
        }
        if($images->save()){
            $images = Image::all();
            return response()->json([
                'success'    => true,
                'images'    => $images,
                'message'      => 'Upload successfully.'
            ]);
        }

    }

    public function delete($id){
        $image = Image::find($id);
        if($image->delete()){
            unlink(storage_path('app/public/images/' . $image->image));
            $success = true;
            $message = "User deleted successfully";
        }else {
            $success = true;
            $message = "User not found";
        }
        $images = Image::all();
        return response()->json([
            'success' => $success,
            'message' => $message,
            'images'    => $images,
        ]);
    }
}
