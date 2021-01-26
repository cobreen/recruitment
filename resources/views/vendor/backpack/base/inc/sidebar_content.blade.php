<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('product') }}"><i class="la la-truck-loading nav-icon"></i> {{ trans('Products') }}</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('cart') }}'><i class='la la-shopping-cart nav-icon'></i>{{ __('Carts') }}</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('user') }}'><i class='la la-user-circle nav-icon'></i> Users</a></li>