<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\SupplierOrderStatus; // Enum del estado del pedido al proveedor
use App\Enums\OrderItemType; // Enum del tipo de order item

//seria product order
class OrderItem extends Model
{

       

    protected $primaryKey = 'id_order_item';

    // Campos asignables en masa
    // - id_set: referencia al set al que pertenece este ítem (si aplica)
    // - id_parent_order_item: referencia al OrderItem "padre" (el item del set). Permite agrupar
    //   todos los OrderItems de productos que pertenecen a la misma instancia de set en el carrito.
    // - only_in_set: indica si este ítem solo se vende como parte de un set (boolean, por defecto false)
    // - supplier_order_date: fecha en que se pidió este ítem al proveedor (date, nullable)
    // - supplier_status: estado del pedido al proveedor (string) -> enum SupplierOrderStatus: not_ordered, ordered, delivered
    protected $fillable = [
        'id_order',
        'id_product',
        'quantity',
        'subtotal',
        'is_customized',
        'custom_text',
        'id_user',
        'id_set',
        'id_parent_order_item',
        'type_order',
        'only_in_set',
        'supplier_order_date',
        'supplier_status',
    ];
    //se creara la orden hasta que se confirme en el carrito id order es null (controlador que crea la orden y coloca el id order de los que estan en el carrito)

    public function order()
    {
        return $this->belongsTo(Order::class, 'id_order');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'id_product');
    }

    public function carts()
    {
        return $this->hasMany(Cart::class, 'id_order_item');
    }

    public function set()
    {
        // Relación con Set usando id_set como FK en order_items y id_set como PK en sets
        return $this->belongsTo(Set::class, 'id_set', 'id_set');
    }

    /**
     * OrderItem padre (el ítem del set) al que pertenece este OrderItem de producto
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'id_parent_order_item', 'id_order_item');
    }

    /**
     * OrderItems hijos (productos) que pertenecen a este OrderItem de set
     */
    public function children()
    {
        return $this->hasMany(self::class, 'id_parent_order_item', 'id_order_item');
    }

    /**
     * Verifica si este item proviene de un set
     */
    public function isFromSet()
    {
        return !is_null($this->id_set);
    }

    protected $casts = [
        'custom_text' => 'array',
        'only_in_set' => 'boolean',
        'supplier_order_date' => 'date',
        // Cast a enum respaldado por string
        'supplier_status' => SupplierOrderStatus::class,
        'type_order' => OrderItemType::class,
    ];
}
