<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\product;
use App\Models\Category;
use Illuminate\Http\Request;
use File;
use Image;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $product = product::orderBy('created_at','desc')->Paginate(10);
        return view('backend.product.index',compact('product'));
    }

    public function createfrom()
    {
        $category = Category::all();
        return view('backend.product.Procreatefrom',compact('category'));
    }

    public function edit($product_id)
    {
        $pro = product::find($product_id);
        $cat = Category::all();
        return view('backend.product.edit',compact('pro','cat'));
    }

    public function insert(Request $request){
         //ป้องกันการกรอกข้อมูลผ่านฟอร์ม
         $validated = $request->validate([
            'name' => 'required|max:255',
            'price' => 'required|max:255',
            'description' => 'required',
            'image' => 'mimes:jpg,jpeg,png',
        ],
    [
        'name.required' => 'กรุณากรอกข้อมูลประเภทสินค้า',
        'name.max' => 'กรอกข้อมูลได้ 255 ตัวอักษร',
        'price.required' => 'กรุณากรอกข้อมูลประเภทสินค้า',
        'price.max' => 'กรอกข้อมูลได้ 255 ตัวอักษร',
        'description.required' => 'กรุณากรอกข้อมูลประเภทสินค้า',
        'description.max' => 'กรอกข้อมูลได้ 255 ตัวอักษร',
        'image.mimes' => 'อัพโหลดรูปภาพที่มีนามสกุล .jpg .jpeg .png ได้เท่านั้น',
    ]);

        $product = new product();
        $product->name = $request->name;    
        $product->price = $request->price;  
        $product->description = $request->description;  
        $product->category_id = $request->category_id;
        if($request->hasFile('image')){
            $filename = Str::random(10).'.'. 
            $request->file('image')->getClientOriginalExtension(); //rty56888.jpg
            $request->file('image')->move(public_path().'/backend/product/', $filename);
            Image::make(public_path().'/backend/product/'. 
            $filename)->resize(500,450)->save(public_path().
            '/backend/product/resize/' .$filename);
            $product->image = $filename;
        }else{
            $product->image = 'no_image.jpg';
        }  
        $product->save();
        alert()->success('บันทึกข้อมูลเสร็จสิ้น','ข้อมูลได้รับการบันทึกแล้ว');
        return redirect('admin/product/index');
    }
    public function update(Request $request, $product_id){
        $product = product::find($product_id);
        $product->name = $request->name;    
        $product->price = $request->price;  
        $product->description = $request->description;  
        $product->category_id = $request->category_id;
        if($request->hasFile('image')){
            if($product->image !='no_image.jpg'){
                File::delete(public_path() .
                '/backend/product/'.$product->image);
                File::delete(public_path() .'/backend/product/resize/'.$product->image);               
            }
            $filename = Str::random(10).'.'. 
            $request->file('image')->getClientOriginalExtension(); //rty56888.jpg
            $request->file('image')->move(public_path().'/backend/product/', $filename);Image::make(public_path().'/backend/product/'. 
            $filename)->resize(500,450)->save(public_path().
            '/backend/product/resize/' .$filename);
            $product->image = $filename;
        }
        $product->Update();
        alert()->success('แก้ไขข้อมูลเสร็จสิ้น','ข้อมูลได้รับการบันทึกแล้ว');
        return redirect('admin/product/index');
    }
    public function delete($product_id){
        $product = product::find($product_id);
        if($product->image != 'no_image.jpg'){
            File::delete(public_path() .
            '/backend/product/'.$product->image);
            File::delete(public_path() .
            '/backend/product/resize/'.$product->image);               
        }
        $product->delete();
        alert()->success('ลบข้อมูลเสร็จสิ้น','ข้อมูลได้รับการบันทึกแล้ว');
        return redirect('admin/product/index');
    }
}