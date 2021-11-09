<?php $count=1; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{$theIP->name}}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <style>
        html {
        /* default font-size is 16px - this is set in the default browser stylesheet */ 
        font-size: 12px; 
        font-weight: 700; 
        line-height: 1; 
        } 
    </style>
</head>

<body>
        <h1 class="text-center mb-3 label label-success">{{$theIP->name}}</h1>

        <div class="alert alert-secondary">
            <p> <strong>Description : </strong> {{$theIP->description}}</p>
           <p><strong>Allergies : </strong> 
                @foreach ($allergies as $allergy)
                    {{$allergy}} , 
                @endforeach
            </p>        
        </div>

        <div class="alert alert-light" role="alert">
            <h3 class="alert-heading"> Ingredients</h3>
            {{-- <hr> --}}
            <table class="table table-bordered table-striped table-sm">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Ingredient</th>
                        <th scope="col">Amount</th>                     
                    </tr>
                </thead>
                <tbody>
                <tr><td colspan="3"><h6>Seasoning</h6></td></tr>
                @foreach($recipes as $recipe)
                    @if($recipe->type == 'Seasoning')
                    <tr>
                        <td><?php print $count++ ?></td>
                        <td>{{$recipe->goods_material_id ? $GMOptions[$recipe->goods_material_id] : $IPOptions[$recipe->inter_p_ingredient_id]}} </td>
                        <td>{{ $recipe->amount }} </td>                     
                    </tr>
                    @endif
                @endforeach
                <?php $count=1; ?>
                <tr><td colspan="3"><h6>Main</h6></td></tr>
                @foreach($recipes as $recipe)
                    @if($recipe->type == 'Main')
                    <tr>
                        <td><?php print $count++ ?></td>
                        <td>{{$recipe->goods_material_id ? $GMOptions[$recipe->goods_material_id] : $IPOptions[$recipe->inter_p_ingredient_id]}} </td>
                        <td>{{ $recipe->amount }} </td>                     
                    </tr>
                    @endif
                @endforeach
                <?php $count=1; ?>
                <tr><td colspan="3"><h6>Aromatic</h6></td></tr>
                @foreach($recipes as $recipe)
                    @if($recipe->type == 'Aromatic')
                    <tr>
                        <td><?php print $count++ ?></td>
                        <td>{{$recipe->goods_material_id ? $GMOptions[$recipe->goods_material_id] : $IPOptions[$recipe->inter_p_ingredient_id]}} </td>
                        <td>{{ $recipe->amount }} </td>                     
                    </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="alert alert-light border" role="alert">
            <h3 class="alert-heading"> Instruction</h3>
            <hr>
            <p>{!! $theIP->recipe !!}</p>
            <hr>
            <p class="mb-0">Whenever you need to, be sure to use margin utilities to keep things nice and tidy.</p>
        </div>
        
       

    </div>

</body>

</html>