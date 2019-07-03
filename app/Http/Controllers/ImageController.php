<?php

namespace App\Http\Controllers;

use App\User;
use App\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{

    public function __construct()
    {
        return $this->middleware('auth');
    }

    public function index(){
        $images = User::where('id', auth()->id())->first()->images;
        return response()->json([
            'images' => $images,
            'message' => true,
        ], 200);
    }


    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:jpeg,jpg,png|min:1|max:20000',
        ]);

        $file = $request->file('file');
        $filename = $file->getClientOriginalName();
        $path = hash( 'sha256', time());

        if(Storage::disk('public')->put($path.$filename,  File::get($file))) {
            $input['filename'] = $filename;
            $input['path'] = $path;
            $input['type'] = $file->getClientMimeType();
            $input['size'] = $file->getSize();
            $addedID = User::where('id', auth()->id())->first()->images()->create($input)->id;
            $image = Image::where('id', $addedID)->first();

            return response()->json([
                'message' => 'uploaded',
                'image' => $image,
            ], 200);
        }
        return response()->json([
            'message' => 'upload false'
        ], 500);

    }

    public function destroy($id){
        $images = User::where('id', auth()->id())->first()->images;
        $image = $images->where('id', $id)->first();
        Storage::disk('public')->delete($image->path.$image->filename);
        $image->delete();
        return response()->json([
            'message' => 'delete',
//            'images' => User::where('id', auth()->id())->first()->images
        ], 200);

    }

}
