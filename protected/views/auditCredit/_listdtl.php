<tr class='clickable-row <?php echo $this->record['style']; ?>' data-href='<?php echo $this->getLink('GA01', 'auditCredit/edit', 'auditCredit/view', array('index'=>$this->record['id']));?>'>


    <td><?php echo $this->drawEditButton('GA01', 'auditCredit/edit', 'edit', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['employee_name']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
    <td><?php echo $this->record['charity_name']; ?></td>
    <td><?php echo $this->record['credit_point']; ?></td>
    <td><?php echo $this->record['apply_date']; ?></td>
    <td><?php echo $this->record['status']; ?></td>
</tr>
