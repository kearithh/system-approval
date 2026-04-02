<?php foreach($paymentHistory as $key=>$value){?>
    <?php
      $obj = json_decode($value->data);
    ?>
    <tr>
        <td>{{ $key +1 }}</td>
        <td>
            <a href="{{url("/uploads/$obj->payment_receipt_file")}}" target="_blank" class="open_link_file">
                {{ @$obj->payment_receipt_file }}
            </a>
        </td>
        <td>{{ @$obj->paid_amount }}</td>
        <td>{{ @$obj->remaining_amount }}</td>
        <td>{{ @$obj->paid_date }}</td>
        <td>{{ @$obj->description }}</td>
        <td>{{ @$value->userCreated->username }}</td>
    </tr>
<?php } ?>
