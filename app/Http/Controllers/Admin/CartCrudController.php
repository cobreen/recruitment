<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CartRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class CartCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CartCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Cart::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/cart');
        CRUD::setEntityNameStrings(__('Cart'), __('Carts'));
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        // CRUD::setFromDb(); // columns

        $this->unifiedColumns();

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
    }

    protected function setupShowOperation()
    {
        $this->unifiedColumns();
    }

    protected function unifiedColumns()
    {
        $this->crud->set('show.setFromDb', false);
        CRUD::addColumn([  
            // any type of relationship
            'name'         => 'user', // name of relationship method in the model
            'type'         => 'relationship',
            'label'        => __('User'), // Table column heading
            // OPTIONAL
            'entity'    => 'user', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
            'model'     => App\Models\User::class, // foreign key model
        ]);

        CRUD::addColumn([  
            // any type of relationship
            'name'         => 'product', // name of relationship method in the model
            'type'         => 'relationship',
            'label'        => __('Product'), // Table column heading
            // OPTIONAL
            'entity'    => 'Product', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
            'model'     => App\Models\Product::class, // foreign key model
        ]);

        CRUD::addColumn([  
            // any type of relationship
            'name'         => 'ammount', // name of relationship method in the model
            'type'         => 'number',
            'label'        => __('Ammount'), // Table column heading
        ]);

        CRUD::addColumn([  
            // any type of relationship
            'name'         => 'Price', // name of relationship method in the model
            'type'         => 'closure',
            'label'        => __('Price'), // Table column heading
            'function'     => function ($entry) {
                return $entry->product->price * $entry->ammount . config('app.character');
            }
        ]);
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(CartRequest::class);

        // CRUD::setFromDb(); // fields
        CRUD::addField([  // Select2
            'label'     => __("User"),
            'type'      => 'select2',
            'name'      => 'user_id', // the db column for the foreign key
            'entity'    => 'user', // the method that defines the relationship in your Model
            'model'     => "App\Models\User", // foreign key model
            'attribute' => 'name', // foreign key attribute that is shown to user
        ]);
        CRUD::addField([  // Select2
            'label'     => __("Product"),
            'type'      => 'select2',
            'name'      => 'product_id', // the db column for the foreign key
            'entity'    => 'product', // the method that defines the relationship in your Model
            'model'     => "App\Models\Product", // foreign key model
            'attribute' => 'name', // foreign key attribute that is shown to user
        ]);
        CRUD::addField([   // Number
            'name' => 'ammount',
            'label' => __('Ammount'),
            'type' => 'number',
        
            // optionals
            'attributes' => ["step" => "1"], // allow decimals
            'suffix'     => __("item(s)"),
        ]);
        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
