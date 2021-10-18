<?php

namespace App\Models;

use ArrayObject;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Stock\Daily_Emp_Work;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Permissions\HasPermissionsTrait;
use Illuminate\Notifications\Notifiable;
use App\Models\Stock\Intermediate_product;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable , HasPermissionsTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'Active',

    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

    public static function boot()
    {
        parent::boot();
        
        // static::creating(function ($user) {
        //     // $user->password = bcrypt($user->password);
        //     $user->password = Hash::make($user->password);
        // });

        // static::updating(function ($user) {
        //     // $user->password = bcrypt($user->password);
        //     $user->password = Hash::make($user->password);
        // });

        
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function receivedLikes()
    {
        return $this->hasManyThrough(Like::class, Post::class);
    }
    

    public function getJWTIdentifier()
    {
        return $this->id;
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    
    public function daily_emp_works()
    {
        return $this->hasMany(Daily_Emp_Work::class);
    }
    // public function intermediate_products()
    // {
    //     return $this->belongsToMany(Intermediate_product::class,'daily_emp_works');
    // }
    public function roles()
    {
        return $this->belongsToMany(Role::class,'users_roles');
    }
    public function permissions()
    {
        return $this->belongsToMany(Permission::class,'users_permissions');
    }
    public function getRoleNames()
    {
        $allRoleNameOfUser = [];
        foreach ($this->roles as $role) {
            array_push($allRoleNameOfUser,$role);
        }   
        return $allRoleNameOfUser;

    }
   
    public function getPermissions()
    {
        $allPermissionsOfUser = [];

         // get all permissions from the role of user
       
        foreach ($this->roles as $role) {
            $permissionsOfTheRole = $role->permissions->map->only(['id', 'name']);
            // return var_dump(json_decode(json_encode($permissionsOfTheRole,true)));
            // $permissionsOfTheRorleArray =  json_decode(json_encode($permissionsOfTheRole,true));
            // array_merge($allPermissionsOfTheRole,$myArray);
            $included = true;
            // get all permissions from the role of user
            foreach($permissionsOfTheRole as $theConsideredPermission) {
                //test if the consider permission is alredy in 
                foreach($allPermissionsOfUser as $thePermission){
                    if($theConsideredPermission['id'] == $thePermission['id']){
                        $included = false;
                    } 
                }
                if($included) {
                    array_push($allPermissionsOfUser,$theConsideredPermission);
                }
            }
        }   

       
        // get all permissions from the the pivot table of user_permission
        $user_permissions = $this->permissions->map->only(['id', 'name']);
   
        $included = true;
        foreach($user_permissions as $theConsideredPermission)
        {
          
            foreach($allPermissionsOfUser as $thePermission){
                if($theConsideredPermission['id'] == $thePermission['id']){
                    $included = false;
                } 
            }
            if($included) {
                array_push($allPermissionsOfUser,$theConsideredPermission);
            }
        }
     

            // $arr = json_decode($permissionsOfTheRole);
            // $arrayobj->append($permissionsOfTheRole);
            // array_push($allPermissionsOfTheRole, $arr);
        

            //  // get all permissions from the role of user
            //  foreach($user_roles as $role){
            //     foreach($role->permissions as $permission){
            //         // $userPermission[$permission->id] = $permission->name;
            //         array_push($userPermissionId,$permission->id);
            //     }
            // }
    
            //  // get all permissions from the the pivot table of user_permission
            //  foreach($user_permissions as $permission)
            //  {
            //      if(!in_array($permission->id,$userPermissionId))    
            //      array_push($userPermissionId,$permission->id);
            //  }

        // return $allPermissionsOfTheRole;
        return $allPermissionsOfUser;
        // return $merge_object;
        // return $arrayobj;

    }

    public function getIntermediateProducts()
    {
         // get all roles of the user from pivot table user_role
         $user_roles = $this->roles;
         // get all permissions of the user from pivot table user_permission
         $user_permissions = $this->permissions;

         $userPermissionId =[];
        
         $returnArr = [];

          // get all permissions from the role of user
        foreach($user_roles as $role){
            foreach($role->permissions as $permission){
                // $userPermission[$permission->id] = $permission->name;
                array_push($userPermissionId,$permission->id);
            }
        }

         // get all permissions from the the pivot table of user_permission
         foreach($user_permissions as $permission)
         {
             if(!in_array($permission->id,$userPermissionId))    
             array_push($userPermissionId,$permission->id);
         }
        
            $ips = Intermediate_Product::select('id','name','required_qty')
            ->whereIn('Preparation',['Yes','OnGoing'])
            ->where('active',1)
            ->get();           
          
        
        
            // lopp to all intermdeiateProducts
            foreach ($ips as $ip){
                $theIP_Permissions = $ip->permissions;
                //loop through all the assigened permission of the im
                foreach($theIP_Permissions as $theGM_Permission){
                    // if the permission of intermediate product is one included in the user permissions
                    if(in_array($theGM_Permission->id,$userPermissionId)) {
                        $returnArr[$ip['id']] = $ip;
                        break;
                    }
                }
            }
            // foreach ($ips as  $ip) {              
            //     $returnArr[$ip['id']] = $ip;


            // }
       
         // }
         // $r_array = json_decode(json_encode($r), true);
 
         // return $r;
         // return $r_array;
         return $returnArr;
         // return $user_roles;
    }
}
