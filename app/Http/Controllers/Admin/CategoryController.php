<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(){
        $category = Category::orderBy('category_id','DESC')->Paginate(5);
        return view('backend.category.index',compact('category'));
}

    public function createfrom(){
        return view('backend.category.createfrom');
    }

    public function edit($category_id){
        $cat = Category::find($category_id);
        return view('backend.category.edit',compact('cat'));
    }

    public function insert(Request $request){
        //ส่วนของการป้องกันการบันทึกข้อมูล
        $validated = $request->validate([
            'name' => 'required|unique:categories|max:255',
        ],
        [
            'name.required'=> 'กรุณากรอกข้อมูลประเภทสินค้า',
            'name.unique'=> 'ชื่อนี้มีอยู่ในฐานข้อมูลอยู่แล้ว',
            'name.max'=> 'กรอกข้อมูลได้ 255 ตัวอักษร',
        ]);
        $category = new Category();
        $category->name = $request->name;
        $category->save();
        alert()->success('บันทึกข้อมูลสำเร็จ','ข้อมูลนี้ถูกบันทึกแล้ว');
        return redirect('admin/category/index'); 
    }

    public function update(Request $request, $category_id){
        $category = Category::find($category_id);
        $category->name = $request->name;
        $category->update();
        alert()->success('แก้ไขข้อมูลสำเร็จ','ข้อมูลนี้ถูกบันทึกแล้ว');
        return redirect('admin/category/index'); 
    }

    public function delete($category_id){
        $category = Category::find($category_id);
        alert()->success('ลบข้อมูลสำเร็จ','ข้อมูลนี้ถูกลบแล้ว');
        return redirect('admin/category/index'); 
    }
}