<?php $count=1; ?>

<h1>Golden Lor Yarrabilba Order</h1>

<h3>Address: Shop 7/16-20 Yarrabilba Dr, Yarrabilba QLD 4207</h3>
<h4>Phone: 0425 647 057 </h4>
<h2>Order To Supplier: {{$supplier->name}}</h2>
<h4> Order Date : {{$date}} </h4>
<h3>Note: Please do delivery within Our Opening Hours: Monday-Friday (11.00 AM - 2.30 PM OR AFTER 4.00 PM)</h3>
<br>

<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Name</th>
        <th>Unit</th>
        <th>Quantity</th>
        <th>Category</th>
    </tr>
    </thead>
    <tbody>
    
    @foreach($good_materials as $good_material)
        {{-- @if($good_material->required_qty > 0) --}}
            <tr>
                {{-- <td>{{$loop->iteration}}</td> --}}
                <td> <?php print $count++ ?> </td>
                {{-- <td>{{ $good_material->name }}</td>
                <td>{{ $good_material->unit->name}}</td>
                <td>{{ $good_material->required_qty }}</td>
                <td>{{ $good_material->category->name}}</td> --}}
                <td>{{ $good_material['name'] }}</td>
                <td>{{ $unitOptions[$good_material['unit_id']] }}</td>
                <td>{{ $good_material['required_qty'] }}</td>
                <td>{{ $categoryOptions[$good_material['category_id']] }}</td>
            </tr>
        {{-- @endif --}}
    @endforeach
    </tbody>
</table>