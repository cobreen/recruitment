<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\productRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class productCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class productCrudController extends CrudController
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
        CRUD::setModel(\App\Models\product::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/product');
        CRUD::setEntityNameStrings(__('Product'), __('Products'));
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
        CRUD::addColumn([
            'name'  => 'name',
            'label' => __('Title'),
            'type'  => 'text'
        ]);
        CRUD::addColumn([
            'name'      => 'image', // The db column name
            'label'     => __('Image'), // Table column heading
            'type'      => 'image',
            'height' => '60px',
            'width'  => '60px',
        ]);
        CRUD::addColumn([
            'name'  => 'price',
            'label' => __('Price'),
            'type'  => 'number'
        ]);
        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
    }

    protected function setupShowOperation()
    {
        CRUD::addColumn([
            'name'  => 'name',
            'label' => __('Title'),
            'type'  => 'text'
        ]);
        CRUD::addColumn([
            'name'      => 'image', // The db column name
            'label'     => __('Image'), // Table column heading
            'type'      => 'image',
            'height' => '150px',
            'width'  => '150px',
        ]);
        CRUD::addColumn([
            'name'  => 'price',
            'label' => __('Price'),
            'type'  => 'number'
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
        CRUD::setValidation(productRequest::class);

        $this->crud->addField([
            'name'  => 'name',
            'label' => __('Title'),
            'type'  => 'text'
        ]);

        $this->crud->addField([
            'label' => __("Image"),
            'name' => "image",
            'type' => 'image',
            'crop' => true, // set to true to allow cropping, false to disable
            'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio
            // 'disk'      => 's3_bucket', // in case you need to show images from a different disk
            // 'prefix'    => 'uploads/images/profile_pictures/' // in case your db value is only the file name (no path), you can use this to prepend your path to the image src (in HTML), before it's shown to the user;
        ]);

        $this->crud->addField([   // Number
            'name' => 'price',
            'label' => __('Number'),
            'type' => 'number',
            'attributes' => ["min" => "0" ,"step" => "0.01"], // allow decimals
            'prefix'     => config('app.character'),
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
