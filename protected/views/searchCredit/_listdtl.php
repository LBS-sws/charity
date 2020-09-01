<tr class='clickable-row' data-href='<?php echo $this->getLink('SR01', 'searchCredit/view', 'searchCredit/view', array('index'=>$this->record['id']));?>'>


    <td><?php echo $this->needHrefButton('SR01', 'searchCredit/view', 'view', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['employee_code']; ?></td>
    <td><?php echo $this->record['employee_name']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
    <td><?php echo $this->record['credit_name']; ?></td>
    <td><?php echo $this->record['credit_point']; ?></td>
    <td><?php echo $this->record['apply_date']; ?></td>
    <td><?php echo $this->record['exp_date']; ?></td>
</tr>
