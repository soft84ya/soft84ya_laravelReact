<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class TempImageController extends Controller
{
    public function store(Request $request){

        $validator = Validator::make($request->all(),[
            'image' => 'required|mimes:png,jpg,jpeg,gif'
        ]);

        if ($validator->fails()){

            return response()->json([
                'status' => false,
                'errors' => $validator->errors('image')
            ]);
        }

        $image = $request->image;

        if(!empty($image)) {

            $ext = $image->getClientOriginalExtension();
            $imageName = strtotime('now').'.'.$ext;

            //save Data in temp image table
            $model = new TempImage();
            $model->name = $imageName;
            $model->save();

            //save image in upload temp/temp directory
            $image->move(public_path('uploads/temp'),$imageName);

                        // create thumb image instance
            $sourcePath = public_path('uploads/temp/'.$imageName);
            $destPath = public_path('uploads/temp/thumb/'.$imageName);
            $manager = new ImageManager(Driver::class);
            $image = $manager->read($sourcePath);
            $image-> coverDown(300,300);
            $image->save($destPath);

            return response()->json([
                'status' => true,
                'data' => $model,
                'errors' => 'Image upload successfully'
            ]);
        }
    }
}
