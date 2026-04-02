<?php foreach($contractHistory as $key=>$value){?>
    <?php
        $con = @$value->contract;
        $property = @$con->propertiesName;
        $property_owner = @$property->proOwner;
        $property_name = json_decode(@$property->data);
        $history = json_decode(@$value->data);
        $user =  @$value->userUpdateBy->username ?? @$value->userCreated->username;
        $action = @$value->userUpdateBy->username ? "Update" :"Create";
        $pro_obj  = @$value->propertyHis;
        $prop = json_decode(@$pro_obj->data);
        $contract_type = Constants::PROPERTY_TYPE[@$history->contract_type]
    ?>
    <tr>
        <td>{{ @$property_owner->name }}</td>
        <td>{{ @$prop->name }}
        <td>
            <a href="{{url("/uploads/$history->attachfile")}}" target="_blank" class="open_link_file">
                {{ @$history->attachfile }}
            </a>
        </td>
        <td>{{ @$contract_type }}</td>
        <td>{{ @$history->full_amount }}</td>
        <td>{{ @$history->effective_date }}</td>
        <td>{{ @$history->due_date }}</td>
        <td>{{ @$user }}</td>
        <td>{{ @$action }}</td>
    </tr>

<?php } ?>
