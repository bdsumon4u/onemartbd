<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class NoteHistory extends Model
{
    protected $fillable = ['order_id', 'text', 'user_id', 'user_type'];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
    public function get_admin()
    {
        return $this->hasOne(Admin::class, 'id', 'user_id')->select('id', 'name');
    }
    public function get_manager()
    {
        return $this->hasOne(Manager::class, 'id', 'user_id')->select('id', 'name');
    }
    public function get_employee()
    {
        return $this->hasOne(Employee::class, 'id', 'user_id')->select('id', 'name');
    }

}
