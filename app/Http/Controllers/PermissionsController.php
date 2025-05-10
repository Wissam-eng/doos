<?php

namespace App\Http\Controllers;

use App\Models\Permissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


use Illuminate\Support\Facades\Validator;
// call Rule
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

use App\Traits\HasPermissionCheck;



class PermissionsController extends Controller
{
    use HasPermissionCheck;

    public function index()
    {


        if ($response = $this->checkPermission('permissions', 'view')) {
            return $response;
        }

        // جلب جميع الأدوار مع الصلاحيات المرتبطة بها
        $rolesWithPermissions = DB::table('roles')
            ->leftJoin('permissions', 'roles.id', '=', 'permissions.role_id')
            ->select('roles.id as role_id', 'roles.role as role_name', 'permissions.permission', 'permissions.add', 'permissions.edit', 'permissions.delete')
            ->whereNull('roles.deleted_at')  // تطبيق whereNull قبل groupBy
            ->get()
            ->groupBy('role_id');

        // إعادة ترتيب البيانات بحيث يحتوي كل رول على الصلاحيات الخاصة به
        $roles = $rolesWithPermissions->map(function ($permissions, $role_id) {
            return [
                'role_id' => $role_id,
                'role_name' => $permissions->first()->role_name,  // جلب اسم الدور من أول صلاحية في المجموعة
                'permissions' => $permissions->map(function ($permission) {
                    return [
                        'permission' => $permission->permission,
                        'add' => $permission->add,
                        'edit' => $permission->edit,
                        'delete' => $permission->delete,
                    ];
                })
            ];
        });

        return response()->json([
            'status' => 'success',
            'roles' => $roles
        ]);
    }



    public function store(Request $request)
    {

        if ($response = $this->checkPermission('permissions', 'add')) {
            return $response;
        }



        // التحقق من صحة البيانات
        $validator = Validator::make($request->all(), [
            'role_id' => [
                'required',
                Rule::exists('roles', 'id')->whereNull('deleted_at'), // التأكد من أن الدور ليس محذوف
            ],
            'permissions' => 'required|array',  // التأكد من أن permissions هي مصفوفة
            'permissions.*.permission' => 'required|string',
            'permissions.*.add' => 'required|in:0,1',   // التأكد من أن القيم 0 أو 1
            'permissions.*.edit' => 'required|in:0,1',
            'permissions.*.delete' => 'required|in:0,1',
        ]);

        // إذا فشلت التحقق من البيانات
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 400);
        }

        // تخزين البيانات في قاعدة البيانات
        $permissionsData = $request->input('permissions');

        // إجراء تخزين متعدد لكل صلاحية
        foreach ($permissionsData as $permission) {
            Permissions::create([
                'role_id' => $request->role_id,  // استخدام role_id من البيانات المستلمة
                'permission' => $permission['permission'],
                'add' => $permission['add'],
                'edit' => $permission['edit'],
                'delete' => $permission['delete'],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Permissions saved successfully',
        ]);
    }



    public function update(Request $request, $role_id)
    {

        if ($response = $this->checkPermission('permissions', 'edit')) {
            return $response;
        }



        // جلب جميع الصلاحيات التي تتوافق مع role_id
        $permissions = Permissions::where('role_id', $role_id)->get();

        // إذا لم يتم العثور على أي صلاحيات بنفس role_id
        if ($permissions->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No permissions found for this role'
            ], 404);
        }

        // التحقق من صحة البيانات
        $validator = Validator::make($request->all(), [
            'role_id' => [
                'required',
                Rule::exists('roles', 'id')->whereNull('deleted_at'), // التأكد من أن الدور ليس محذوف
            ],
            'permissions' => 'required|array',  // التأكد من أن permissions هي مصفوفة
            'permissions.*.permission' => 'required|string',  // التحقق من وجود قيمة لكل صلاحية
            'permissions.*.add' => 'required|in:0,1',   // التأكد من أن القيم 0 أو 1
            'permissions.*.edit' => 'required|in:0,1',
            'permissions.*.delete' => 'required|in:0,1',
        ]);

        // إذا فشلت التحقق من البيانات
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 400);
        }

        // تحديث كل صلاحية بناءً على البيانات المستلمة
        foreach ($request->permissions as $updatedPermission) {
            $permission = $permissions->firstWhere('permission', $updatedPermission['permission']);

            if ($permission) {
                $permission->update([
                    'permission' => $updatedPermission['permission'],  // تحديث اسم الصلاحية
                    'add' => $updatedPermission['add'],  // تحديث قيمة add
                    'edit' => $updatedPermission['edit'],  // تحديث قيمة edit
                    'delete' => $updatedPermission['delete'],  // تحديث قيمة delete
                ]);
            }
        }

        // العودة بالاستجابة مع الصلاحيات المحدثة
        return response()->json([
            'status' => 'success',
            'message' => 'Permissions updated successfully',
            'permissions' => $permissions
        ]);
    }



    public function destroy($role_id)
    {


        if ($response = $this->checkPermission('permissions', 'delete')) {
            return $response;
        }

        // جلب جميع الصلاحيات التي تتوافق مع role_id
        $permissions = Permissions::where('role_id', $role_id);

        // إذا لم يتم العثور على أي صلاحيات بنفس role_id
        if ($permissions->count() == 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'No permissions found for this role'
            ], 404);
        }

        // حذف جميع الصلاحيات التي تتوافق مع role_id
        $permissions->delete();

        // العودة بالاستجابة مع رسالة النجاح
        return response()->json([
            'status' => 'success',
            'message' => 'Permissions deleted successfully'
        ]);
    }
}
