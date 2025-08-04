<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;

trait Upload
{
    public function uploadFile($file, $path)
    {

        if ($file) {
            $filename = time() . rand(1, 99) . '.' . $file->getClientOriginalExtension();
            $upload_path = "uploads/$path";
            $file->move(public_path($upload_path), $filename);
            return $upload_path . '/' . $filename;
        }
    }
    public function deleteFile($path)
    {
        if(File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
        return true;
    }
}

// $data['photo'] = $this->uploadFile($request->file('photo'), 'profile');
// $this->deleteFile($user->photo);
